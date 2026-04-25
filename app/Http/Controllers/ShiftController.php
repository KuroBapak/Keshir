<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::withCount('users')->orderBy('start_time')->get();
        
        $staffUsers = User::with('role', 'defaultShift')
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'owner'))
            ->get();
            
        return view('dashboard.shifts.index', compact('shifts', 'staffUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_threshold' => 'required|integer|min:0',
            'color' => 'nullable|string|max:50',
        ]);

        Shift::create($validated);

        return back()->with('success', 'Shift berhasil ditambahkan.');
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_threshold' => 'required|integer|min:0',
            'color' => 'nullable|string|max:50',
        ]);

        $shift->update($validated);

        return back()->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        if ($shift->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus shift karena masih digunakan oleh karyawan.');
        }

        if ($shift->attendanceLogs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus shift karena ada data absensi yang menggunakannya.');
        }

        $shift->delete();

        return back()->with('success', 'Shift berhasil dihapus.');
    }

    public function assignStaff(Request $request, User $user)
    {
        $request->validate([
            'default_shift_id' => 'nullable|exists:shifts,id',
        ]);

        $user->update([
            'default_shift_id' => $request->default_shift_id,
            'allow_double_shift' => $request->has('allow_double_shift'),
        ]);

        return back()->with('success', "Pengaturan shift untuk {$user->name} berhasil diperbarui.");
    }
}
