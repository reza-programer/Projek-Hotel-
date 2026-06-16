<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otp = sprintf("%06d", mt_rand(1, 999999));

        DB::table('otp_verifications')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Store registration data in session temporarily
        $request->session()->put('pending_registration', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            Mail::to($request->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            // Log error but proceed for local testing if SMTP fails
            \Illuminate\Support\Facades\Log::error('OTP Email failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'OTP sent to email.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $verification = DB::table('otp_verifications')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$verification) {
            throw ValidationException::withMessages(['otp' => 'Kode OTP tidak valid.']);
        }

        if (now()->greaterThan($verification->expires_at)) {
            throw ValidationException::withMessages(['otp' => 'Kode OTP sudah kedaluwarsa.']);
        }

        $pending = $request->session()->get('pending_registration');

        if (!$pending || $pending['email'] !== $request->email) {
            throw ValidationException::withMessages(['email' => 'Sesi pendaftaran tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => $pending['password'],
        ]);

        DB::table('otp_verifications')->where('email', $request->email)->delete();
        $request->session()->forget('pending_registration');

        Auth::login($user);

        return response()->json(['success' => true, 'message' => 'Registrasi berhasil.']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return response()->json(['success' => true]);
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }
}
