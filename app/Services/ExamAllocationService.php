<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ExamAllocationService
{

    public function allocateExams(): void
    {
        DB::transaction(function () {

            DB::table('classroom_exam')->delete();
            Exam::query()->update(['supervisor_id' => null]);

            $exams = Exam::with(['department'])->orderBy('id')->get();
            $classrooms = Classroom::all();

            foreach ($exams as $exam) {
                if (!$exam->start_time || !$exam->end_time) {
                    $this->assignDateToExam($exam, $classrooms);
                }

                $compatibleClassrooms = [];

                foreach ($classrooms as $classroom) {
                    $hasOverlap = DB::table('classroom_exam')
                        ->join('exams', 'classroom_exam.exam_id', '=', 'exams.id')
                        ->where('classroom_exam.classroom_id', $classroom->id)
                        ->where('exams.start_time', '<', $exam->end_time)
                        ->where('exams.end_time', '>', $exam->start_time)
                        ->exists();

                    if (!$hasOverlap) {
                        $compatibleClassrooms[] = $classroom;
                    }
                }

                if (empty($compatibleClassrooms)) {
                    throw new Exception(
                        "Uyuşmayan Sınav Planı: '{$exam->name}' sınavı (Öğrenci Sayısı: {$exam->student_count}, " .
                        "Zaman: {$exam->start_time->format('d.m.Y H:i')} - {$exam->end_time->format('H:i')}) " .
                        "için uygun kapasite veya zaman diliminde boş derslik bulunamadı."
                    );
                }

                $singleRooms = array_filter($compatibleClassrooms, function ($c) use ($exam) {
                    return $c->capacity >= $exam->student_count;
                });

                $allocatedClassroomIds = [];

                if (!empty($singleRooms)) {
                    $bestClassroom = null;
                    $minWaste = PHP_INT_MAX;

                    foreach ($singleRooms as $classroom) {
                        $waste = $classroom->capacity - $exam->student_count;
                        if ($waste < $minWaste) {
                            $minWaste = $waste;
                            $bestClassroom = $classroom;
                        }
                    }

                    $allocatedClassroomIds[] = $bestClassroom->id;
                } else {
                    usort($compatibleClassrooms, function ($a, $b) {
                        return $b->capacity <=> $a->capacity;
                    });

                    $currentCapacity = 0;
                    foreach ($compatibleClassrooms as $classroom) {
                        $allocatedClassroomIds[] = $classroom->id;
                        $currentCapacity += $classroom->capacity;

                        if ($currentCapacity >= $exam->student_count) {
                            break;
                        }
                    }

                    if ($currentCapacity < $exam->student_count) {
                        throw new Exception(
                            "Uyuşmayan Sınav Planı: '{$exam->name}' sınavı (Öğrenci Sayısı: {$exam->student_count}, " .
                            "Zaman: {$exam->start_time->format('d.m.Y H:i')} - {$exam->end_time->format('H:i')}) " .
                            "için yeterli toplam kapasite (Boş Kapasite: {$currentCapacity}) bulunamadı."
                        );
                    }
                }

                $exam->classrooms()->sync($allocatedClassroomIds);

                $supervisor = $this->assignSupervisor($exam);

                if ($supervisor === null) {
                    throw new Exception(
                        "Gözetmen Bulunamadı: '{$exam->name}' sınavı ({$exam->start_time->format('d.m.Y H:i')} - {$exam->end_time->format('H:i')}) " .
                        "için bölümde uygun gözetmen bulunamadı. Tüm hocalar bu saatte başka sınavda gözetmenlik yapıyor."
                    );
                }

                $exam->update(['supervisor_id' => $supervisor->id]);
            }
        });
    }

    private array $lastDepartmentExamDate = [];

    private function assignDateToExam(Exam $exam, $classrooms): void
    {
        $period = \App\Models\ExamPeriod::getForDepartment($exam->department_id);
        if (!$period) {
            throw new Exception("Sınav Haftası Bulunamadı: '{$exam->name}' sınavına otomatik tarih atayabilmek için bölümün veya fakültenin sınav haftası tanımlanmış olmalıdır.");
        }

        $duration = 120;
        $timeSlots = ['09:00', '11:00', '13:00', '15:00'];
        $startDate = $period->start_date->copy();
        $endDate   = $period->end_date->copy();

        $current = $startDate->copy();
        if (isset($this->lastDepartmentExamDate[$exam->department_id])) {
            $proposed = $this->lastDepartmentExamDate[$exam->department_id]->copy()->addDays(2)->startOfDay();
            if ($proposed->lte($endDate)) {
                $current = $proposed;
            }
        }

        $daysToTry = [];
        $curr = $current->copy();
        while ($curr->lte($endDate)) {
            $daysToTry[] = $curr->copy();
            $curr->addDay();
        }
        if ($current->gt($startDate)) {
            $curr = $startDate->copy();
            $limit = $current->copy()->subDay();
            while ($curr->lte($limit)) {
                $daysToTry[] = $curr->copy();
                $curr->addDay();
            }
        }

        foreach ($daysToTry as $day) {
            if ($day->isWeekend()) {
                continue;
            }

            $dailyExamCount = Exam::where('department_id', $exam->department_id)
                ->whereDate('start_time', $day->format('Y-m-d'))
                ->count();

            if ($dailyExamCount >= 3) {
                continue;
            }

            foreach ($timeSlots as $slotStart) {
                [$h, $m]   = explode(':', $slotStart);
                $startTime = $day->copy()->setTime((int)$h, (int)$m);
                $endTime   = $startTime->copy()->addMinutes($duration);

                $instructorBusy = Exam::where('instructor_id', $exam->instructor_id)
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->where('id', '!=', $exam->id)
                    ->exists();

                if ($instructorBusy) {
                    continue;
                }

                $availableCapacity = 0;
                foreach ($classrooms as $classroom) {
                    $hasOverlap = DB::table('classroom_exam')
                        ->join('exams', 'classroom_exam.exam_id', '=', 'exams.id')
                        ->where('classroom_exam.classroom_id', $classroom->id)
                        ->where('exams.start_time', '<', $endTime)
                        ->where('exams.end_time', '>', $startTime)
                        ->exists();

                    if (!$hasOverlap) {
                        $availableCapacity += $classroom->capacity;
                    }
                }

                if ($availableCapacity >= $exam->student_count) {
                    $exam->update([
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                    ]);
                    $exam->start_time = $startTime;
                    $exam->end_time = $endTime;

                    $this->lastDepartmentExamDate[$exam->department_id] = $day->copy();
                    return;
                }
            }
        }

        throw new Exception("Uygun Zaman Bulunamadı: '{$exam->name}' sınavı için {$period->start_date->format('d.m.Y')} - {$period->end_date->format('d.m.Y')} tarihleri arasında hem dersi veren eğitmenin boş olduğu hem de yeterli derslik kapasitesinin bulunduğu bir zaman dilimi bulunamadı.");
    }

    private function assignSupervisor(Exam $exam): ?User
    {
        $departmentInstructors = User::where('department_id', $exam->department_id)
            ->where('role', 'egitmen')
            ->where('status', 'approved')
            ->get();

        if ($departmentInstructors->isEmpty()) {
            return null;
        }

        $busySupervisorIds = Exam::where('start_time', '<', $exam->end_time)
            ->where('end_time', '>', $exam->start_time)
            ->whereNotNull('supervisor_id')
            ->pluck('supervisor_id')
            ->toArray();

        $availableInstructors = $departmentInstructors->filter(function ($instructor) use ($busySupervisorIds) {
            return !in_array($instructor->id, $busySupervisorIds);
        });

        if ($availableInstructors->isEmpty()) {
            return null;
        }

        $supervisorCounts = Exam::whereIn('supervisor_id', $availableInstructors->pluck('id'))
            ->whereNotNull('supervisor_id')
            ->groupBy('supervisor_id')
            ->selectRaw('supervisor_id, COUNT(*) as count')
            ->pluck('count', 'supervisor_id')
            ->toArray();

        $bestSupervisor = null;
        $minCount = PHP_INT_MAX;

        foreach ($availableInstructors as $instructor) {
            $count = $supervisorCounts[$instructor->id] ?? 0;
            if ($count < $minCount) {
                $minCount = $count;
                $bestSupervisor = $instructor;
            }
        }

        return $bestSupervisor;
    }
}
