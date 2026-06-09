<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
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

        $buildings = Building::orderBy('name')->get();

        return view('admin.buildings.index', compact('buildings'));
    }

    public function create()
    {
        $this->checkAccess();
        return view('admin.buildings.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:buildings,name'],
            'code' => ['nullable', 'string', 'max:50', 'unique:buildings,code'],
        ]);

        Building::create($validated);

        return redirect()->route('buildings.index')
            ->with('success', 'Bina başarıyla oluşturuldu.');
    }

    public function edit(Building $building)
    {
        $this->checkAccess();
        return view('admin.buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:buildings,name,' . $building->id],
            'code' => ['nullable', 'string', 'max:50', 'unique:buildings,code,' . $building->id],
        ]);

        $building->update($validated);

        return redirect()->route('buildings.index')
            ->with('success', 'Bina başarıyla güncellendi.');
    }

    public function destroy(Building $building)
    {
        $this->checkAccess();

        $classroomCount = $building->classrooms()->count();
        if ($classroomCount > 0) {
            return redirect()->route('buildings.index')
                ->with('error', "Bu binada {$classroomCount} derslik bulunduğu için silinemez.");
        }

        $building->delete();

        return redirect()->route('buildings.index')
            ->with('success', 'Bina başarıyla silindi.');
    }
}
