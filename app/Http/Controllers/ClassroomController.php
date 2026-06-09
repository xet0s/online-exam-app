<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Building;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClassroomController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isDean()) {
            $classrooms = Classroom::with('department')->orderBy('name')->get();
        } else {
            $classrooms = Classroom::where('department_id', $user->department_id)
                ->with('department')
                ->orderBy('name')
                ->get();
        }

        return view('classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        Gate::authorize('create', Classroom::class);

        $departments = Department::orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();
        return view('classrooms.create', compact('departments', 'buildings'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Classroom::class);

        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'building' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];

        if ($user->isAdmin() || $user->isDean()) {
            $rules['department_id'] = ['nullable', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        if (!$user->isAdmin() && !$user->isDean()) {
            $validated['department_id'] = $user->department_id;
        }

        Classroom::create($validated);

        return redirect()->route('classrooms.index')
            ->with('success', 'Derslik başarıyla oluşturuldu.');
    }

    public function edit(Classroom $classroom)
    {
        Gate::authorize('update', $classroom);

        $departments = Department::orderBy('name')->get();
        $buildings = Building::orderBy('name')->get();
        return view('classrooms.edit', compact('classroom', 'departments', 'buildings'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        Gate::authorize('update', $classroom);

        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'building' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];

        if ($user->isAdmin() || $user->isDean()) {
            $rules['department_id'] = ['nullable', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        if (!$user->isAdmin() && !$user->isDean()) {
            $validated['department_id'] = $user->department_id;
        }

        $classroom->update($validated);

        return redirect()->route('classrooms.index')
            ->with('success', 'Derslik başarıyla güncellendi.');
    }

    public function destroy(Classroom $classroom)
    {
        Gate::authorize('delete', $classroom);

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Derslik başarıyla silindi.');
    }
}
