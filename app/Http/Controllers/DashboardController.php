<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamPeriod;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        $pdfStatus = Cache::get("pdf_status_{$user->id}", 'idle');

        $stats = [];
        $examsByDepartment = collect(); // bölüme göre gruplu sınavlar
        $classrooms = [];

        if ($user->isAdmin() || $user->isDean()) {
            $stats = [
                'departments_count' => Department::count(),
                'classrooms_count'  => Classroom::count(),
                'exams_count'       => Exam::count(),
                'instructors_count' => User::where('role', 'egitmen')->count(),
                'pending_count'     => User::where('status', 'pending')->count(),
            ];

            // Tüm bölümleri al ve her birinin sınavlarını grupla
            $departments = Department::withCount('exams')->orderBy('name')->get();

            $allExams = Exam::with(['instructor', 'supervisor', 'department', 'classrooms'])
                ->orderBy('start_time')
                ->get();

            $examsByDepartment = $departments->map(function ($dept) use ($allExams) {
                return [
                    'department' => $dept,
                    'exams'      => $allExams->where('department_id', $dept->id)->values(),
                ];
            });

            $classrooms = Classroom::with('department')->orderBy('name')->get();

        } elseif ($user->isChair()) {
            $deptId = $user->department_id;

            $stats = [
                'classrooms_count'  => Classroom::where('department_id', $deptId)->count(),
                'exams_count'       => Exam::where('department_id', $deptId)->count(),
                'instructors_count' => User::where('role', 'egitmen')->where('department_id', $deptId)->count(),
                'pending_count'     => User::where('status', 'pending')->where('department_id', $deptId)->count(),
            ];

            $dept = Department::find($deptId);
            $deptExams = Exam::with(['instructor', 'supervisor', 'department', 'classrooms'])
                ->where('department_id', $deptId)
                ->orderBy('start_time')
                ->get();

            $examsByDepartment = collect([
                [
                    'department' => $dept,
                    'exams'      => $deptExams,
                ]
            ]);

            $classrooms = Classroom::where('department_id', $deptId)->orderBy('name')->get();

        } elseif ($user->isInstructor()) {
            $deptId = $user->department_id;
            $dept   = Department::find($deptId);

            $deptExams = Exam::with(['instructor', 'supervisor', 'department', 'classrooms'])
                ->where(function ($query) use ($user) {
                    $query->where('instructor_id', $user->id)
                        ->orWhere(function ($q) use ($user) {
                            $q->where('department_id', $user->department_id)
                              ->where('status', 'approved');
                        });
                })
                ->orderBy('start_time')
                ->get();

            $examsByDepartment = collect([
                [
                    'department' => $dept,
                    'exams'      => $deptExams,
                ]
            ]);
        }

        // Tüm tanımlı sınav haftalarını al (dashboard paneli için)
        $allPeriods = ExamPeriod::with(['department', 'creator'])->get();

        return view('dashboard', compact('stats', 'examsByDepartment', 'classrooms', 'pdfStatus', 'allPeriods'));
    }
}
