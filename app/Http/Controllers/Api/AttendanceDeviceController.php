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

        $uid = $request->uid;
        $user = User::where('rfid_uid', $uid)->first();

        if (!$user) {
            return response()->json(['status' => 'unknown_card']);
        }

        $today = now()->startOfDay();
        $log = \App\Models\AttendanceLog::where('user_id', $user->id)
            ->where('created_at', '>=', $today)
            ->first();

        if (!$log) {
            // Check-in
            \App\Models\AttendanceLog::create([
                'user_id' => $user->id,
                'check_in' => now(),
                'status' => 'present',
                'source' => 'iot',
            ]);

            return response()->json([
                'status' => 'check_in',
                'name' => $user->name,
                'time' => now()->format('H:i'),
            ]);
        } else {
            // Jika sudah check-out
            if ($log->check_out) {
                return response()->json([
                    'status' => 'already_done',
                    'name' => $user->name,
                ]);
            }

            // Cooldown 5 menit
            $checkInTime = \Carbon\Carbon::parse($log->check_in);
            $diffMins = $checkInTime->diffInMinutes(now());

            if ($diffMins < 5) {
                return response()->json([
                    'status' => 'cooldown',
                    'name' => $user->name,
                    'remaining' => 5 - $diffMins,
                ]);
            }

            // Check-out
            $log->update([
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
    }
}
