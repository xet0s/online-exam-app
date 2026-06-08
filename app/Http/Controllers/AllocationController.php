<?php

namespace App\Http\Controllers;

use App\Services\ExamAllocationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllocationController extends Controller
{

    public function run(Request $request, ExamAllocationService $allocationService)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isDean()) {
            abort(403, 'Bu işlemi sadece Admin veya Dekan yapabilir.');
        }

        try {
            $allocationService->allocateExams();
            return redirect()->route('dashboard')
                ->with('success', 'Sınav derslik tahsisatı başarıyla tamamlandı.');
        } catch (Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', $e->getMessage());
        }
    }
}
