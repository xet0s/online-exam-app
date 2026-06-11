<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ExamPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamPeriodController extends Controller
{
    /**
     * Sınav haftası oluştur veya mevcut olanı güncelle (upsert).
     * Bölüm başkanı: sadece kendi bölümü için.
     * Admin/Dekan: istedikleri bölüm veya fakülte geneli için.
     */
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

        // Bölüm başkanı yalnızca kendi bölümü için tanımlayabilir
        if ($user->isChair()) {
            $validated['department_id'] = $user->department_id;
        }

        // Admin/dekan department_id boş bırakırsa fakülte geneli olur
        $departmentId = $validated['department_id'] ?? null;

        // Varsa güncelle, yoksa oluştur
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

    /**
     * Sınav haftasını sil.
     */
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

    /**
     * Belirli bir bölüm için mevcut sınav dönemini döndürür (JSON).
     * Sınav formu tarafından AJAX ile kullanılır.
     */
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
