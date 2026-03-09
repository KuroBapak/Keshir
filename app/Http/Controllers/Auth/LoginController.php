<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CashDrawer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
        }

        $user = Auth::user();

        // Owner bypass attendance check
        if (!$user->isOwner()) {
            $hasCheckedIn = \App\Models\AttendanceLog::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->whereNotNull('check_in')
                ->exists();

            if (!$hasCheckedIn) {
                Auth::logout();
                return back()->withErrors(['username' => 'Anda belum absen hari ini. Silakan check-in dulu di halaman absensi.'])->withInput();
            }
        }

        $request->session()->regenerate();

        // Flash shift warning for cashier
        if ($user->role->name === 'cashier') {
            $hasShift = CashDrawer::where('user_id', $user->id)
                ->where('status', 'open')
                ->exists();

            if (!$hasShift) {
                session()->flash('warning', '⚠️ Anda belum membuka shift hari ini. Silakan buka shift sebelum melakukan transaksi.');
            }
        }

        return redirect()->intended($this->redirectByRole($user));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Warn cashier about unclosed shift
        if ($user && $user->role->name === 'cashier') {
            $hasOpenShift = CashDrawer::where('user_id', $user->id)
                ->where('status', 'open')
                ->exists();

            if ($hasOpenShift && !$request->has('force_logout')) {
                return back()->with('warning', '⚠️ Anda masih memiliki shift aktif! Tutup shift di Kas Laci sebelum logout. <a href="' . route('cash-drawer.index') . '" style="font-weight:700;">Buka Kas Laci</a> atau <form action="' . route('logout') . '" method="POST" style="display:inline;"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="force_logout" value="1"><button type="submit" style="background:none;border:none;color:inherit;text-decoration:underline;cursor:pointer;font-weight:700;">Paksa Logout</button></form>');
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Determine redirect path based on user role.
     */
    private function redirectByRole($user): string
    {
        return match ($user->role->name) {
            'owner', 'manager' => '/dashboard',
            'cashier' => '/pos',
            'kitchen_staff' => '/kitchen',
            default => '/dashboard',
        };
    }
}
