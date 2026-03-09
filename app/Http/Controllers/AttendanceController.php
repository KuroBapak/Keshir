<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Show the temporary web attendance page.
     */
    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'owner'))
            ->get();

        $todayLogs = AttendanceLog::with('user')
            ->where('date', now()->toDateString())
            ->get()
            ->keyBy('user_id');

        return view('attendance.temp', compact('users', 'todayLogs'));
    }

    /**
     * Check-in a staff user.
     */
    public function checkIn(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $log = AttendanceLog::firstOrCreate(
            [
                'user_id' => $request->user_id,
                'date' => now()->toDateString(),
            ],
            [
                'check_in' => now(),
                'source' => 'web',
            ]
        );

        if ($log->wasRecentlyCreated) {
            return back()->with('success', 'Check-in berhasil!');
        }

        return back()->with('info', 'Sudah check-in hari ini.');
    }

    /**
     * Check-out a staff user.
     */
    public function checkOut(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $log = AttendanceLog::where('user_id', $request->user_id)
            ->where('date', now()->toDateString())
            ->first();

        if (!$log || !$log->check_in) {
            return back()->with('error', 'Belum check-in hari ini.');
        }

        if ($log->check_out) {
            return back()->with('info', 'Sudah check-out hari ini.');
        }

        $log->update(['check_out' => now()]);

        return back()->with('success', 'Check-out berhasil!');
    }
}
