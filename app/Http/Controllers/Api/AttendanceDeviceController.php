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

        // Calculate status_in based on shift
        $statusIn = 'on_time';
        if ($user->default_shift_id && $user->defaultShift) {
            $shift = $user->defaultShift;
            $nowTime = now()->format('H:i:s');
            
            // Calculate late threshold time
            $thresholdTime = \Carbon\Carbon::parse($shift->start_time)->addMinutes($shift->late_threshold)->format('H:i:s');
            
            if ($nowTime > $thresholdTime) {
                $statusIn = 'late';
            }
        }

        // Check-in
        \App\Models\AttendanceLog::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'check_in' => now(),
            'source' => 'iot',
            'shift_id' => $user->default_shift_id,
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
