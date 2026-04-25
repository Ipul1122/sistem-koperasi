<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpLoginController extends Controller
{
    // 1. Tampilkan form masukin email
    public function requestForm()
    {
        return view('auth.otp-request');
    }

    // 2. Kirim OTP ke email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di sistem.']);
        }

        // Generate 6 digit angka random
        $otp = rand(100000, 999999);

        // Simpan OTP ke cache selama 5 menit
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

        // Kirim email
        Mail::to($request->email)->send(new OtpMail($otp));

        // Pindah ke halaman verifikasi dengan membawa data email
        return redirect()->route('otp.verify')->with('email', $request->email);
    }

    // 3. Tampilkan form verifikasi OTP
    public function verifyForm()
    {
        if (!session('email')) {
            return redirect()->route('otp.request');
        }
        return view('auth.otp-verify');
    }

    // 4. Proses verifikasi dan login
    public function processVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email);

        if ($request->otp == $cachedOtp) {
            Cache::forget('otp_' . $request->email); // Hapus cache biar aman
            
            $user = User::where('email', $request->email)->first();

            // Set email_verified_at karena user sudah membuktikan kepemilikan email via OTP
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }

            Auth::login($user); // Login otomatis

            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.'])->with('email', $request->email);
    }
}