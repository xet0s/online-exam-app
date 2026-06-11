<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExamPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamPeriodController extends Controller
{

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isDean() && !$user->isChair()) {
            abort(403, 'Sınav haftası tanımlamak için yetkiniz yoktur.');
        }

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        
        if ($user->isChair()) {
            $validated['department_id'] = $user->department_id;
        }

        
        $departmentId = $validated['department_id'] ?? null;

        
        ExamPeriod::updateOrCreate(
            ['department_id' => $departmentId],
            [
                'name'       => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date'],
                'created_by' => $user->id,
            ]
        );

        return redirect()->route('dashboard')
            ->with('success', 'Sınav haftası başarıyla kaydedildi.');
    }

    
    public function destroy(ExamPeriod $examPeriod)
    {
        $user = Auth::user();

        if ($user->isChair() && $examPeriod->department_id !== $user->department_id) {
            abort(403);
        }

        if (!$user->isAdmin() && !$user->isDean() && !$user->isChair()) {
            abort(403);
        }

        $examPeriod->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Sınav haftası silindi.');
    }

    
    public function getForDepartment(Request $request)
    {
        $departmentId = $request->input('department_id');
        $period = ExamPeriod::getForDepartment($departmentId ? (int) $departmentId : null);

        if (!$period) {
            return response()->json(['period' => null]);
        }

        return response()->json([
            'period' => [
                'id'         => $period->id,
                'name'       => $period->name,
                'start_date' => $period->start_date->format('Y-m-d'),
                'end_date'   => $period->end_date->format('Y-m-d'),
                'label'      => $period->start_date->format('d.m.Y') . ' – ' . $period->end_date->format('d.m.Y'),
            ],
        ]);
    }
}
