<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ApprovalController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $departments = Department::orderBy('name')->get();

        if ($user->isAdmin() || $user->isDean()) {
            $pendingUsers = User::where('status', 'pending')
                ->with('department')
                ->latest()
                ->get();

            $approvedUsers = User::where('status', 'approved')
                ->with('department')
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        } else {

            $pendingUsers = User::where('status', 'pending')
                ->where('department_id', $user->department_id)
                ->with('department')
                ->latest()
                ->get();

            $approvedUsers = User::where('status', 'approved')
                ->where('department_id', $user->department_id)
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get();
        }

        return view('admin.approval', compact('pendingUsers', 'approvedUsers', 'departments'));
    }

    public function approve(Request $request, User $targetUser)
    {
        Gate::authorize('approve', $targetUser);

        $targetUser->update([
            'status' => 'approved',
        ]);

        return redirect()->route('approvals.index')
            ->with('success', "{$targetUser->name} kullanıcısı onaylandı ve sisteme erişim yetkisi verildi.");
    }

    public function reject(Request $request, User $targetUser)
    {
        Gate::authorize('reject', $targetUser);

        $name = $targetUser->name;
        $targetUser->delete();

        return redirect()->route('approvals.index')
            ->with('success', "{$name} kullanıcısının kaydı silindi.");
    }

    public function storePreApproved(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isDean()) {
            abort(403, 'Bu işlemi sadece Admin veya Dekan yapabilir.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,dekan,bolum_baskani,egitmen'],
            'department_id' => ['required_unless:role,admin,dekan', 'nullable', 'exists:departments,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'approved',
            'department_id' => in_array($request->role, ['admin', 'dekan']) ? null : $request->department_id,
        ]);

        return redirect()->route('approvals.index')
            ->with('success', "Kullanıcı başarıyla ön onaylı olarak oluşturuldu.");
    }
}
