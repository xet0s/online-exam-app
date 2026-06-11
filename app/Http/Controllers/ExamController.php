<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamPeriod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ExamController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isDean()) {
            $exams = Exam::with(['instructor', 'supervisor', 'department', 'classrooms'])
                ->orderBy('start_time')
                ->get();
        } else {
            $exams = Exam::where('department_id', $user->department_id)
                ->with(['instructor', 'supervisor', 'department', 'classrooms'])
                ->orderBy('start_time')
                ->get();
        }

        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        Gate::authorize('create', Exam::class);

        $user = Auth::user();

        if ($user->isAdmin() || $user->isDean()) {
            $instructors = User::where('role', 'egitmen')
                ->where('status', 'approved')
                ->orderBy('name')
                ->get();
            $departments = Department::orderBy('name')->get();
            $classrooms = Classroom::orderBy('name')->get();
        } else {
            // Eğitmen kendi adını görür; bölüm başkanı/müdür bölüm hocalarını görür
            if ($user->isInstructor()) {
                $instructors = User::where('id', $user->id)->get();
            } else {
                $instructors = User::where('role', 'egitmen')
                    ->where('status', 'approved')
                    ->where('department_id', $user->department_id)
                    ->orderBy('name')
                    ->get();
            }
            $departments = collect();
            $classrooms = Classroom::where('department_id', $user->department_id)
                ->orderBy('name')
                ->get();
        }

        return view('exams.create', compact('instructors', 'departments', 'classrooms'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Exam::class);

        $user = Auth::user();

        // Eğitmen kendi sınavını giriyorsa instructor_id otomatik atanır
        if ($user->isInstructor()) {
            $request->merge(['instructor_id' => $user->id]);
        }

        $date      = $request->input('date');
        $startHour = $request->input('start_hour');
        $endHour   = $request->input('end_hour');

        if ($date && $startHour && $endHour) {
            $request->merge([
                'start_time' => $date . ' ' . $startHour,
                'end_time'   => $date . ' ' . $endHour,
            ]);
        }

        $rules = [
            'name'            => ['required', 'string', 'max:255'],
            'student_count'   => ['required', 'integer', 'min:1'],
            'start_time'      => ['nullable', 'date'],
            'end_time'        => ['nullable', 'date', 'after:start_time'],
            'instructor_id'   => ['required', 'exists:users,id'],
            'classroom_ids'   => ['nullable', 'array'],
            'classroom_ids.*' => ['exists:classrooms,id'],
        ];

        if ($user->isAdmin() || $user->isDean()) {
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        if (!$user->isAdmin() && !$user->isDean()) {
            $validated['department_id'] = $user->department_id;
        }

        $instructor = User::findOrFail($validated['instructor_id']);
        if (!$instructor->isInstructor() || !$instructor->isApproved()) {
            return back()->withErrors(['instructor_id' => 'Seçilen eğitmen onaylı değil ya da eğitmen rolünde değil.'])->withInput();
        }

        if (!$user->isAdmin() && !$user->isDean()) {
            if ($instructor->department_id !== $user->department_id) {
                return back()->withErrors(['instructor_id' => 'Kendi bölümünüz dışından bir eğitmen atayamazsınız.'])->withInput();
            }
            if (!empty($request->input('classroom_ids'))) {
                $classroomIds = $request->input('classroom_ids');
                $invalidClassroomsCount = Classroom::whereIn('id', $classroomIds)
                    ->where('department_id', '!=', $user->department_id)
                    ->count();
                if ($invalidClassroomsCount > 0) {
                    return back()->withErrors(['classroom_ids' => 'Kendi bölümünüz dışından bir derslik atayamazsınız.'])->withInput();
                }
            }
        }

        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            // ─── Çakışma Kontrolü: Dersi Veren Hocanın Aynı Saatte Başka Sınavı ────
            $instructorConflict = Exam::where('instructor_id', $validated['instructor_id'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();

            if ($instructorConflict) {
                return back()->withErrors(['instructor_id' => 'Seçilen eğitmenin bu saat aralığında başka bir sınavı bulunmaktadır. Lütfen farklı bir saat seçin.'])->withInput();
            }

            // ─── Çakışma Kontrolü: Derslik ──────────────────────────────────────────
            if (!empty($request->input('classroom_ids'))) {
                $classroomIds = $request->input('classroom_ids');
                $occupied = DB::table('classroom_exam')
                    ->join('exams', 'classroom_exam.exam_id', '=', 'exams.id')
                    ->whereIn('classroom_exam.classroom_id', $classroomIds)
                    ->where('exams.start_time', '<', $validated['end_time'])
                    ->where('exams.end_time', '>', $validated['start_time'])
                    ->pluck('classroom_exam.classroom_id')
                    ->toArray();

                if (!empty($occupied)) {
                    $occupiedNames = Classroom::whereIn('id', $occupied)->pluck('name')->implode(', ');
                    return back()->withErrors(['classroom_ids' => "Seçilen şu derslik(ler) belirtilen saatler arasında doludur: {$occupiedNames}."])->withInput();
                }
            }
        }

        if ($user->isAdmin() || $user->isDean()) {
            $status = 'approved';
        } elseif ($user->isChair()) {
            $status = 'sent_to_dean';
        } else {
            $status = 'pending';
        }

        $examAttributes            = collect($validated)->except('classroom_ids')->toArray();
        $examAttributes['status']  = $status;
        $exam = Exam::create($examAttributes);

        if (!empty($request->input('classroom_ids'))) {
            $exam->classrooms()->sync($request->input('classroom_ids'));
        }

        return redirect()->route('exams.index')
            ->with('success', 'Sınav başarıyla oluşturuldu.');
    }

    public function edit(Exam $exam)
    {
        Gate::authorize('update', $exam);

        $user = Auth::user();
        $exam->load('classrooms');

        if ($user->isAdmin() || $user->isDean()) {
            $instructors = User::where('role', 'egitmen')
                ->where('status', 'approved')
                ->orderBy('name')
                ->get();
            $departments = Department::orderBy('name')->get();
            $classrooms  = Classroom::orderBy('name')->get();
        } else {
            if ($user->isInstructor()) {
                $instructors = User::where('id', $user->id)->get();
            } else {
                $instructors = User::where('role', 'egitmen')
                    ->where('status', 'approved')
                    ->where('department_id', $user->department_id)
                    ->orderBy('name')
                    ->get();
            }
            $departments = collect();
            $classrooms  = Classroom::where('department_id', $user->department_id)
                ->orderBy('name')
                ->get();
        }

        return view('exams.edit', compact('exam', 'instructors', 'departments', 'classrooms'));
    }

    public function update(Request $request, Exam $exam)
    {
        Gate::authorize('update', $exam);

        $user = Auth::user();

        // Eğitmen kendi sınavını güncelliyorsa instructor_id değişmez
        if ($user->isInstructor()) {
            $request->merge(['instructor_id' => $user->id]);
        }

        $date      = $request->input('date');
        $startHour = $request->input('start_hour');
        $endHour   = $request->input('end_hour');

        if ($date && $startHour && $endHour) {
            $request->merge([
                'start_time' => $date . ' ' . $startHour,
                'end_time'   => $date . ' ' . $endHour,
            ]);
        }

        $rules = [
            'name'            => ['required', 'string', 'max:255'],
            'student_count'   => ['required', 'integer', 'min:1'],
            'start_time'      => ['nullable', 'date'],
            'end_time'        => ['nullable', 'date', 'after:start_time'],
            'instructor_id'   => ['required', 'exists:users,id'],
            'classroom_ids'   => ['nullable', 'array'],
            'classroom_ids.*' => ['exists:classrooms,id'],
        ];

        if ($user->isAdmin() || $user->isDean()) {
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        if (!$user->isAdmin() && !$user->isDean()) {
            $validated['department_id'] = $user->department_id;
        }

        $instructor = User::findOrFail($validated['instructor_id']);
        if (!$instructor->isInstructor() || !$instructor->isApproved()) {
            return back()->withErrors(['instructor_id' => 'Seçilen eğitmen onaylı değil.'])->withInput();
        }

        if (!$user->isAdmin() && !$user->isDean()) {
            if ($instructor->department_id !== $user->department_id) {
                return back()->withErrors(['instructor_id' => 'Kendi bölümünüz dışından bir eğitmen atayamazsınız.'])->withInput();
            }
            if (!empty($request->input('classroom_ids'))) {
                $classroomIds = $request->input('classroom_ids');
                $invalidClassroomsCount = Classroom::whereIn('id', $classroomIds)
                    ->where('department_id', '!=', $user->department_id)
                    ->count();
                if ($invalidClassroomsCount > 0) {
                    return back()->withErrors(['classroom_ids' => 'Kendi bölümünüz dışından bir derslik atayamazsınız.'])->withInput();
                }
            }
        }

        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            // ─── Çakışma Kontrolü: Dersi Veren Hocanın Aynı Saatte Başka Sınavı ────
            $instructorConflict = Exam::where('instructor_id', $validated['instructor_id'])
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->where('id', '!=', $exam->id)
                ->exists();

            if ($instructorConflict) {
                return back()->withErrors(['instructor_id' => 'Seçilen eğitmenin bu saat aralığında başka bir sınavı bulunmaktadır. Lütfen farklı bir saat seçin.'])->withInput();
            }

            // ─── Çakışma Kontrolü: Derslik ──────────────────────────────────────────
            if (!empty($request->input('classroom_ids'))) {
                $classroomIds = $request->input('classroom_ids');
                $occupied = DB::table('classroom_exam')
                    ->join('exams', 'classroom_exam.exam_id', '=', 'exams.id')
                    ->whereIn('classroom_exam.classroom_id', $classroomIds)
                    ->where('exams.start_time', '<', $validated['end_time'])
                    ->where('exams.end_time', '>', $validated['start_time'])
                    ->where('exams.id', '!=', $exam->id)
                    ->pluck('classroom_exam.classroom_id')
                    ->toArray();

                if (!empty($occupied)) {
                    $occupiedNames = Classroom::whereIn('id', $occupied)->pluck('name')->implode(', ');
                    return back()->withErrors(['classroom_ids' => "Seçilen şu derslik(ler) belirtilen saatler arasında doludur: {$occupiedNames}."])->withInput();
                }
            }
        }

        if ($user->isAdmin() || $user->isDean()) {
            $status = 'approved';
        } elseif ($user->isChair()) {
            $status = 'sent_to_dean';
        } else {
            $status = 'pending';
        }

        $examAttributes           = collect($validated)->except('classroom_ids')->toArray();
        $examAttributes['status'] = $status;
        $exam->update($examAttributes);

        $exam->classrooms()->sync($request->input('classroom_ids') ?? []);

        return redirect()->route('exams.index')
            ->with('success', 'Sınav başarıyla güncellendi.');
    }

    public function destroy(Exam $exam)
    {
        Gate::authorize('delete', $exam);

        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Sınav başarıyla silindi.');
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date'        => ['required', 'date_format:Y-m-d'],
            'start_hour'  => ['required', 'date_format:H:i'],
            'end_hour'    => ['required', 'date_format:H:i'],
            'exam_id'     => ['nullable', 'integer'],
            'instructor_id' => ['nullable', 'integer'],
        ]);

        $date        = $request->input('date');
        $startTime   = $date . ' ' . $request->input('start_hour');
        $endTime     = $date . ' ' . $request->input('end_hour');
        $examId      = $request->input('exam_id');
        $instructorId = $request->input('instructor_id');

        // Doluluk durumundaki derslik ID'leri
        $occupiedClassroomIds = DB::table('classroom_exam')
            ->join('exams', 'classroom_exam.exam_id', '=', 'exams.id')
            ->where('exams.start_time', '<', $endTime)
            ->where('exams.end_time', '>', $startTime)
            ->when($examId, function ($query) use ($examId) {
                return $query->where('exams.id', '!=', $examId);
            })
            ->pluck('classroom_exam.classroom_id')
            ->unique()
            ->values();

        // Hoca çakışması: bu saatte dersi olan başka bir sınav var mı?
        $instructorConflict = false;
        if ($instructorId) {
            $instructorConflict = Exam::where('instructor_id', $instructorId)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->when($examId, function ($query) use ($examId) {
                    return $query->where('id', '!=', $examId);
                })
                ->exists();
        }

        return response()->json([
            'occupied_classroom_ids' => $occupiedClassroomIds,
            'instructor_conflict'    => $instructorConflict,
        ]);
    }

    public function approveByChair(Exam $exam)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !($user->isChair() && $user->department_id === $exam->department_id)) {
            abort(403, 'Bu sınavı onaylama yetkiniz yoktur.');
        }

        if ($exam->status !== 'pending') {
            return back()->with('error', 'Sınav onaylanacak durumda değil.');
        }

        $exam->update([
            'status' => 'sent_to_dean',
        ]);

        return back()->with('success', 'Sınav onaylandı ve Dekan onayına iletildi.');
    }

    public function approveByDean(Exam $exam)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isDean()) {
            abort(403, 'Bu sınavı onaylama yetkiniz yoktur.');
        }

        if ($exam->status !== 'sent_to_dean') {
            return back()->with('error', 'Sınav Dekan onayı bekleyen durumda değil.');
        }

        $exam->update([
            'status' => 'approved',
        ]);

        return back()->with('success', 'Sınav başarıyla onaylandı ve takvime eklendi.');
    }

    /**
     * Sınav haftası içinde, ilk uygun tarih/saat dilimini öner.
     * Çakışma kontrolü: aynı hoca + aynı bölüm sınavı çakışmaması.
     */
    public function suggestDateTime(Request $request)
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'duration'      => ['nullable', 'integer', 'min:30', 'max:480'], // dakika
            'exam_id'       => ['nullable', 'integer'],
        ]);

        $departmentId = (int) $request->input('department_id');
        $instructorId = $request->input('instructor_id');
        $duration     = (int) ($request->input('duration', 120)); // varsayılan 2 saat
        $examId       = $request->input('exam_id');

        // Sınav haftasını al
        $period = ExamPeriod::getForDepartment($departmentId);

        if (!$period) {
            return response()->json([
                'error' => 'Bu bölüm için tanımlı bir sınav haftası bulunamadı. Lütfen önce sınav haftasını tanımlayın.',
            ], 422);
        }

        // Günlük sabit zaman dilimleri (başlangıç saatleri)
        $timeSlots = ['09:00', '11:00', '13:00', '15:00'];

        $startDate = $period->start_date->copy();
        $endDate   = $period->end_date->copy();

        // Son atanan sınavı bul ki onu baz alarak 2 gün ileri atalım
        $lastExam = \App\Models\Exam::where('department_id', $departmentId)
            ->whereNotNull('start_time')
            ->orderBy('start_time', 'desc')
            ->first();

        $current = $startDate->copy();
        if ($lastExam) {
            $proposed = $lastExam->start_time->copy()->addDays(2)->startOfDay();
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
            // Hafta sonlarını atla (isteğe bağlı - kaldırmak için bu bloğu silin)
            if ($day->isWeekend()) {
                continue;
            }

            // O gün o bölüm için en fazla 3 sınav kısıtlaması
            $dailyExamCount = \App\Models\Exam::where('department_id', $departmentId)
                ->whereDate('start_time', $day->format('Y-m-d'))
                ->count();

            if ($dailyExamCount >= 3) {
                continue;
            }

            foreach ($timeSlots as $slotStart) {
                [$h, $m]   = explode(':', $slotStart);
                $startTime = $day->copy()->setTime((int)$h, (int)$m);
                $endTime   = $startTime->copy()->addMinutes($duration);

                // Günün sonunu aşıyorsa (19:00+) bu dilimleri atla
                if ($endTime->hour >= 19 || ($endTime->hour === 18 && $endTime->minute > 0)) {
                    continue;
                }

                $startStr = $startTime->format('Y-m-d H:i:s');
                $endStr   = $endTime->format('Y-m-d H:i:s');

                // ─ Hoca çakışma kontrolü ─────────────────────────────
                $instructorBusy = false;
                if ($instructorId) {
                    $instructorBusy = \App\Models\Exam::where('instructor_id', $instructorId)
                        ->where('start_time', '<', $endStr)
                        ->where('end_time', '>', $startStr)
                        ->when($examId, fn($q) => $q->where('id', '!=', $examId))
                        ->exists();
                }

                if ($instructorBusy) continue;

                // ─ Bölüm sınavı çakışma kontrolü ─────────────────────
                $deptConflictCount = \App\Models\Exam::where('department_id', $departmentId)
                    ->where('start_time', '<', $endStr)
                    ->where('end_time', '>', $startStr)
                    ->when($examId, fn($q) => $q->where('id', '!=', $examId))
                    ->count();

                // Bu dilimde uygunsa öner
                return response()->json([
                    'date'              => $day->format('Y-m-d'),
                    'start_hour'        => $startTime->format('H:i'),
                    'end_hour'          => $endTime->format('H:i'),
                    'date_label'        => $day->translatedFormat('l, d F Y'),
                    'period_label'      => $period->start_date->format('d.m.Y') . ' – ' . $period->end_date->format('d.m.Y'),
                    'dept_conflict_count' => $deptConflictCount,
                ]);
            }
        }

        return response()->json([
            'error' => 'Sınav haftası boyunca (' . $period->start_date->format('d.m.Y') . ' – ' . $period->end_date->format('d.m.Y') . ') bu eğitmen için uygun boş zaman dilimi bulunamadı.',
        ], 422);
    }
}

