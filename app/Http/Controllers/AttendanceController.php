<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\User;
use Carbon\Carbon;
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
     * Attendance management for owner/manager.
     * Shows history, stats, and counting.
     */
    public function management(Request $request)
    {
        // Staff users (non-owner)
        $staffUsers = User::with(['role', 'defaultShift'])
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'owner'))
            ->get();

        // Date range filter (default: current month)
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $filterUser = $request->get('user_id', '');

        // Query attendance logs
        $query = AttendanceLog::with(['user.role', 'shift'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc');

        if ($filterUser) {
            $query->where('user_id', $filterUser);
        }

        $logs = $query->get();

        // === TODAY's STATUS ===
        $todayLogs = AttendanceLog::with('user')
            ->where('date', now()->toDateString())
            ->get()
            ->keyBy('user_id');

        $todayPresent = $todayLogs->filter(fn($l) => $l->check_in && !$l->check_out)->count();
        $todayDone = $todayLogs->filter(fn($l) => $l->check_out)->count();
        $todayAbsent = $staffUsers->count() - $todayLogs->count();

        // === PERIOD STATS ===
        // Untuk bisnis F&B / POS, biasanya operasional setiap hari (termasuk weekend).
        $totalWorkDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        // Expected attendance = work days * staff count
        $staffCount = $filterUser ? 1 : $staffUsers->count();
        $expectedAttendance = $totalWorkDays * $staffCount;

        $totalPresent = $logs->count();
        $totalCompleted = $logs->filter(fn($l) => $l->check_in && $l->check_out)->count();
        $totalAlpha = max(0, $expectedAttendance - $totalPresent);

        // Average work duration (only completed shifts)
        $completedLogs = $logs->filter(fn($l) => $l->check_in && $l->check_out);
        $totalMinutes = $completedLogs->sum(function ($log) {
            return $log->check_in->diffInMinutes($log->check_out);
        });
        $avgMinutes = $completedLogs->count() > 0 ? round($totalMinutes / $completedLogs->count()) : 0;
        $avgHours = floor($avgMinutes / 60);
        $avgMins = $avgMinutes % 60;

        // Attendance rate
        $attendanceRate = $expectedAttendance > 0
            ? round(($totalPresent / $expectedAttendance) * 100, 1)
            : 0;

        // Per-staff monthly recap (for the selected period)
        $staffRecap = [];
        foreach ($staffUsers as $staff) {
            if ($filterUser && $staff->id != $filterUser) continue;
            
            $staffLogs = $logs->where('user_id', $staff->id);
            $staffCompleted = $staffLogs->filter(fn($l) => $l->check_in && $l->check_out);
            $staffTotalMinutes = $staffCompleted->sum(fn($l) => $l->check_in->diffInMinutes($l->check_out));
            
            $staffRecap[] = [
                'user' => $staff,
                'total_present' => $staffLogs->count(),
                'total_completed' => $staffCompleted->count(),
                'total_alpha' => max(0, $totalWorkDays - $staffLogs->count()),
                'avg_hours' => $staffCompleted->count() > 0
                    ? round($staffTotalMinutes / $staffCompleted->count() / 60, 1)
                    : 0,
                'total_hours' => round($staffTotalMinutes / 60, 1),
            ];
        }

        $stats = [
            'today_present' => $todayPresent,
            'today_done' => $todayDone,
            'today_absent' => $todayAbsent,
            'total_present' => $totalPresent,
            'total_completed' => $totalCompleted,
            'total_alpha' => $totalAlpha,
            'avg_hours' => $avgHours,
            'avg_mins' => $avgMins,
            'attendance_rate' => $attendanceRate,
            'work_days' => $totalWorkDays,
            'staff_count' => $staffCount,
        ];

        $shifts = \App\Models\Shift::all();

        // Group logs by date
        $logsByDate = $logs->groupBy('date');

        return view('dashboard.attendance.index', compact(
            'logs', 'logsByDate', 'staffUsers', 'stats', 'staffRecap',
            'startDate', 'endDate', 'filterUser', 'todayLogs', 'shifts'
        ));
    }

    /**
     * Check-in a staff user.
     */
    public function checkIn(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = User::with('defaultShift')->findOrFail($request->user_id);
        
        $now = now();
        $statusIn = 'on_time';
        $shiftToAssign = $user->defaultShift;

        // Find the most appropriate active shift
        $shifts = \App\Models\Shift::all();
        $closestShift = null;
        $minDiff = PHP_INT_MAX;
        
        foreach ($shifts as $s) {
            $start = \Carbon\Carbon::parse($now->toDateString() . ' ' . $s->start_time);
            $end = \Carbon\Carbon::parse($now->toDateString() . ' ' . $s->end_time);
            if ($s->end_time < $s->start_time) {
                $end->addDay();
            }
            
            if ($now->between($start->copy()->subHours(2), $end)) {
                $diff = abs($now->diffInMinutes($start));
                if ($diff < $minDiff) {
                    $minDiff = $diff;
                    $closestShift = $s;
                }
            }
        }

        if ($closestShift) {
            $shiftToAssign = $closestShift;
        }

        $shiftId = $shiftToAssign ? $shiftToAssign->id : null;

        if ($shiftToAssign) {
            $thresholdTime = \Carbon\Carbon::parse($shiftToAssign->start_time)
                ->addMinutes($shiftToAssign->late_threshold);
            
            $thresholdFull = \Carbon\Carbon::parse($now->toDateString() . ' ' . $thresholdTime->format('H:i:s'));
            
            if ($now->greaterThan($thresholdFull)) {
                $statusIn = 'late';
            }
        }

        // Check if there is an active check-in today
        $latestLog = AttendanceLog::where('user_id', $request->user_id)
            ->where('date', $now->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestLog && is_null($latestLog->check_out)) {
            return back()->with('info', 'Anda sudah check-in dan belum check-out.');
        }

        if ($latestLog && !is_null($latestLog->check_out) && !$user->allow_double_shift) {
            return back()->with('info', 'Anda sudah menyelesaikan shift hari ini.');
        }

        $log = AttendanceLog::create([
            'user_id' => $request->user_id,
            'date' => $now->toDateString(),
            'check_in' => $now,
            'source' => 'web',
            'shift_id' => $shiftId,
            'status_in' => $statusIn,
        ]);

        return back()->with('success', 'Check-in berhasil! Shift: ' . ($shiftToAssign ? $shiftToAssign->name : 'Default'));
    }

    /**
     * Check-out a staff user.
     */
    public function checkOut(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $log = AttendanceLog::where('user_id', $request->user_id)
            ->where('date', now()->toDateString())
            ->orderBy('created_at', 'desc')
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

    /**
     * Reset check-out for a staff user.
     * Owner & Manager can do this, but manager cannot reset their own check-out.
     */
    public function resetCheckout(AttendanceLog $attendanceLog)
    {
        $currentUser = auth()->user();

        // Manager cannot reset their own check-out (must ask owner)
        if (!$currentUser->isOwner() && $attendanceLog->user_id === $currentUser->id) {
            return back()->with('error', 'Anda tidak bisa mereset check-out milik sendiri. Hubungi Owner.');
        }

        if (!$attendanceLog->check_out) {
            return back()->with('info', 'Record ini belum memiliki check-out.');
        }

        $staffName = $attendanceLog->user->name ?? 'Unknown';
        $attendanceLog->update(['check_out' => null]);

        return back()->with('success', "Check-out untuk {$staffName} berhasil direset. Staff bisa melanjutkan kerja.");
    }

    public function destroy(AttendanceLog $attendanceLog)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isOwner()) {
            abort(403, 'Hanya Owner yang bisa menghapus data absensi.');
        }

        $staffName = $attendanceLog->user->name ?? 'Unknown';
        $date = $attendanceLog->date;
        $attendanceLog->delete();

        return back()->with('success', "Data absensi {$staffName} tanggal {$date} berhasil dihapus.");
    }
}
