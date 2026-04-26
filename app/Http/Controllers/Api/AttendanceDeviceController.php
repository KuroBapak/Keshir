<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceDeviceController extends Controller
{
    /**
     * Daftarkan RFID UID ke User
     */
    public function registerCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'rfid_uid' => 'required|string|unique:users,rfid_uid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);
        $user->rfid_uid = $request->rfid_uid;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil didaftarkan untuk ' . $user->name,
            'data' => $user
        ]);
    }

    /**
     * Proses Tap Absensi Harian dari ESP32 (HTTP POST)
     */
    public function tap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        $user = User::with('defaultShift')->where('rfid_uid', $request->uid)->first();

        if (!$user) {
            return response()->json(['status' => 'unknown_card']);
        }

        // Get the latest log for today
        $latestLog = \App\Models\AttendanceLog::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        // If there is an open log, do checkout
        if ($latestLog && is_null($latestLog->check_out)) {
            // Cooldown 5 minutes
            $checkInTime = \Carbon\Carbon::parse($latestLog->check_in);
            $diffMins = $checkInTime->diffInMinutes(now());

            if ($diffMins < 5) {
                return response()->json([
                    'status' => 'cooldown',
                    'name' => $user->name,
                    'remaining' => 5 - $diffMins,
                ]);
            }

            // Check-out
            $latestLog->update([
                'check_out' => now(),
            ]);

            $duration = $checkInTime->diffInHours(now());

            return response()->json([
                'status' => 'check_out',
                'name' => $user->name,
                'duration' => "{$duration}j",
                'time' => now()->format('H:i'),
            ]);
        }

        // If we reach here, it's a check-in.
        // First check if they already have a completed log today and double shift is not allowed
        if ($latestLog && !is_null($latestLog->check_out) && !$user->allow_double_shift) {
            return response()->json([
                'status' => 'already_done',
                'name' => $user->name,
            ]);
        }

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
            
            // Allow checking in from 2 hours before start until end of shift
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
            
            // Re-parse threshold today for accurate comparison
            $thresholdFull = \Carbon\Carbon::parse($now->toDateString() . ' ' . $thresholdTime->format('H:i:s'));
            
            if ($now->greaterThan($thresholdFull)) {
                $statusIn = 'late';
            }
        }

        // Check-in
        \App\Models\AttendanceLog::create([
            'user_id' => $user->id,
            'date' => $now->toDateString(),
            'check_in' => $now,
            'source' => 'iot',
            'shift_id' => $shiftId,
            'status_in' => $statusIn,
        ]);

        return response()->json([
            'status' => 'check_in',
            'name' => $user->name,
            'time' => now()->format('H:i'),
            'status_in' => $statusIn
        ]);
    }
}
