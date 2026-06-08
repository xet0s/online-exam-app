<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{

    protected function checkManageAccess()
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isDean() && !$user->isChair()) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }
    }

    public function index()
    {
        $this->checkManageAccess();
        $user = Auth::user();

        $query = User::with('department');

        if ($user->isAdmin()) {

            $users = $query->orderByRaw("CASE WHEN role = 'admin' THEN 1 WHEN role = 'dekan' THEN 2 WHEN role = 'bolum_baskani' THEN 3 ELSE 4 END")
                ->orderBy('name')
                ->get();
        } elseif ($user->isDean()) {

            $users = $query->whereIn('role', ['bolum_baskani', 'egitmen'])
                ->orderByRaw("CASE WHEN role = 'bolum_baskani' THEN 1 ELSE 2 END")
                ->orderBy('name')
                ->get();
        } else {

            $users = $query->where('role', 'egitmen')
                ->where('department_id', $user->department_id)
                ->orderBy('name')
                ->get();
        }

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->checkManageAccess();
        $user = Auth::user();

        $departments = Department::orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $this->checkManageAccess();
        $user = Auth::user();

        $allowedRoles = ['egitmen'];
        if ($user->isAdmin()) {
            $allowedRoles = ['admin', 'dekan', 'bolum_baskani', 'egitmen'];
        } elseif ($user->isDean()) {
            $allowedRoles = ['bolum_baskani', 'egitmen'];
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:' . implode(',', $allowedRoles)],
        ];

        if ($user->isChair()) {
            $request->merge(['status' => 'pending']);
        } else {
            $rules['status'] = ['required', 'in:approved,pending'];
        }

        if ($user->isChair()) {
            $request->merge(['department_id' => $user->department_id]);
        } else {
            $rules['department_id'] = ['required_unless:role,admin,dekan', 'nullable', 'exists:departments,id'];
        }

        $request->validate($rules);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $user->isChair() ? 'pending' : $request->status,
            'department_id' => in_array($request->role, ['admin', 'dekan']) ? null : (
                $user->isChair() ? $user->department_id : $request->department_id
            ),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    public function edit(User $targetUser)
    {
        $this->checkManageAccess();
        $user = Auth::user();

        if ($user->isDean()) {
            if (in_array($targetUser->role, ['admin', 'dekan'])) {
                abort(403, 'Bu kullanıcıyı düzenleme yetkiniz yoktur.');
            }
        } elseif ($user->isChair()) {
            if ($targetUser->role !== 'egitmen' || $targetUser->department_id !== $user->department_id) {
                abort(403, 'Bu kullanıcıyı düzenleme yetkiniz yoktur.');
            }
        }

        $departments = Department::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'targetUser', 'departments'));
    }

    public function update(Request $request, User $targetUser)
    {
        $this->checkManageAccess();
        $user = Auth::user();

        if ($user->isDean()) {
            if (in_array($targetUser->role, ['admin', 'dekan'])) {
                abort(403, 'Bu kullanıcıyı düzenleme yetkiniz yoktur.');
            }
        } elseif ($user->isChair()) {
            if ($targetUser->role !== 'egitmen' || $targetUser->department_id !== $user->department_id) {
                abort(403, 'Bu kullanıcıyı düzenleme yetkiniz yoktur.');
            }
        }

        $allowedRoles = ['egitmen'];
        if ($user->isAdmin()) {
            $allowedRoles = ['admin', 'dekan', 'bolum_baskani', 'egitmen'];
        } elseif ($user->isDean()) {
            $allowedRoles = ['bolum_baskani', 'egitmen'];
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $targetUser->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:' . implode(',', $allowedRoles)],
        ];

        if ($user->isChair()) {
            $request->merge(['status' => $targetUser->status]);
        } else {
            $rules['status'] = ['required', 'in:approved,pending'];
        }

        if ($user->isChair()) {
            $request->merge(['department_id' => $user->department_id]);
        } else {
            $rules['department_id'] = ['required_unless:role,admin,dekan', 'nullable', 'exists:departments,id'];
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $user->isChair() ? $targetUser->status : $request->status,
            'department_id' => in_array($request->role, ['admin', 'dekan']) ? null : (
                $user->isChair() ? $user->department_id : $request->department_id
            ),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $targetUser->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $targetUser)
    {
        $this->checkManageAccess();
        $user = Auth::user();

        if ($targetUser->id === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        if ($user->isDean()) {
            if (in_array($targetUser->role, ['admin', 'dekan'])) {
                abort(403, 'Bu kullanıcıyı silme yetkiniz yoktur.');
            }
        } elseif ($user->isChair()) {
            if ($targetUser->role !== 'egitmen' || $targetUser->department_id !== $user->department_id) {
                abort(403, 'Bu kullanıcıyı silme yetkiniz yoktur.');
            }
        }

        $targetUser->delete();

        return redirect()->route('users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
