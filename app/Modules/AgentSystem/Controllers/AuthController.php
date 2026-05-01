<?php
// app/Modules/AgentSystem/Controllers/AuthController.php
namespace App\Modules\AgentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'agent' => $user->agent,
            'email_verified' => (bool) $user->email_verified,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('agent'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // STEP 1: Kirim OTP
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|string|exists:users,whatsapp',
        ]);

        // Hapus OTP lama
        OtpCode::where('whatsapp', $request->whatsapp)
            ->where('type', 'reset_password')
            ->delete();

        // Buat OTP baru
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'whatsapp'   => $request->whatsapp,
            'code'       => $code,
            'type'       => 'reset_password',
            'is_used'    => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Simulasi — log saja dulu
        Log::info("OTP Reset Password untuk {$request->whatsapp}: {$code}");

        return response()->json([
            'message' => 'Kode OTP telah dikirim ke WhatsApp kamu.',
            'debug_otp' => app()->isLocal() ? $code : null, // hanya tampil di local
        ]);
    }

    // STEP 2: Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|string',
            'code'     => 'required|string|size:6',
        ]);

        $otp = OtpCode::where('whatsapp', $request->whatsapp)
            ->where('code', $request->code)
            ->where('type', 'reset_password')
            ->where('is_used', false)
            ->first();

        if (!$otp || !$otp->isValid()) {
            return response()->json([
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'
            ], 422);
        }

        // Buat reset token sementara
        $resetToken = Str::random(64);
        $otp->update([
            'is_used' => true,
            'code'    => $resetToken, // simpan token di field code
        ]);

        return response()->json([
            'message'      => 'OTP valid.',
            'reset_token'  => $resetToken,
        ]);
    }

    // STEP 3: Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token'          => 'required|string',
            'password'             => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $otp = OtpCode::where('code', $request->reset_token)
            ->where('type', 'reset_password')
            ->where('is_used', true)
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Token tidak valid.'], 422);
        }

        // Update password user
        $user = User::where('whatsapp', $otp->whatsapp)->first();
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus OTP
        $otp->delete();

        return response()->json(['message' => 'Password berhasil direset. Silakan login.']);
    }

    // Kirim OTP verifikasi email
    public function sendEmailVerification(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        // Hapus OTP lama
        OtpCode::where('whatsapp', $user->email)
            ->where('type', 'email_verification')
            ->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'whatsapp'   => $user->email, // pakai field whatsapp untuk simpan email
            'code'       => $code,
            'type'       => 'email_verification',
            'is_used'    => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Simulasi — log dulu
        Log::info("OTP Verifikasi Email untuk {$user->email}: {$code}");

        return response()->json([
            'message'   => 'Kode OTP dikirim ke email kamu.',
            'debug_otp' => app()->isLocal() ? $code : null,
        ]);
    }

    // Verifikasi OTP email
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        $otp = OtpCode::where('whatsapp', $user->email)
            ->where('code', $request->code)
            ->where('type', 'email_verification')
            ->where('is_used', false)
            ->first();

        if (!$otp || !$otp->isValid()) {
            return response()->json([
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'
            ], 422);
        }

        // Tandai email sebagai terverifikasi
        $user->update([
            'email_verified'    => true,
            'email_verified_at' => now(),
        ]);

        $otp->update(['is_used' => true]);

        return response()->json(['message' => 'Email berhasil diverifikasi!']);
    }
}
