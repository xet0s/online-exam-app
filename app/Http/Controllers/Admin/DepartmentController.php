<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    protected function checkAccess(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isDean()) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }
    }

    public function index()
    {
        $this->checkAccess();

        $departments = Department::withCount(['users', 'classrooms', 'exams'])
            ->orderBy('name')
            ->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Bölüm başarıyla oluşturuldu.');
    }

    public function edit(Department $department)
    {
        $this->checkAccess();
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Bölüm başarıyla güncellendi.');
    }

    public function destroy(Department $department)
    {
        $this->checkAccess();

        $usersCount = $department->users()->count();
        if ($usersCount > 0) {
            return redirect()->route('departments.index')
                ->with('error', "Bu bölüme bağlı {$usersCount} kullanıcı bulunduğu için silinemez.");
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Bölüm başarıyla silindi.');
    }
}
