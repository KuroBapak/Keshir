<?php

namespace App\Http\Middleware;

use App\Models\AttendanceLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAttendance
{
    /**
     * Handle an incoming request.
     *
     * Users who have checked out for the day are blocked from accessing
     * the application until the next day. Owners bypass this check.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Owners bypass attendance checks
        if ($user && $user->isOwner()) {
            return $next($request);
        }

        if ($user) {
            $log = AttendanceLog::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->orderBy('created_at', 'desc')
                ->first();

            // If user has checked out, block access and force logout
            if ($log && $log->check_out) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['username' => 'Shift Anda sudah selesai untuk hari ini (sudah check-out). Silakan kembali besok.']);
            }

            // If user hasn't checked in yet, block access
            if (!$log || !$log->check_in) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['username' => 'Anda belum absen hari ini. Silakan check-in dulu di halaman absensi.']);
            }
        }

        return $next($request);
    }
}
