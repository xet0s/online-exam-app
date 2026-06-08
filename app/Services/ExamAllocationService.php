<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Exam;
use Exception;
use Illuminate\Support\Facades\DB;

class ExamAllocationService
{

    public function allocateExams(): void
    {
        DB::transaction(function () {

            DB::table('classroom_exam')->delete();

            $exams = Exam::orderBy('start_time')->get();

            $classrooms = Classroom::all();

            foreach ($exams as $exam) {
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
            }
        });
    }
}
