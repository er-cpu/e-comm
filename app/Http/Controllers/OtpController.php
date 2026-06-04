<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(0|\+255)\d{9}$/'],
        ]);

        $user = $request->user();
        $phone = $request->phone;

        // Invalidate any existing unused OTPs for this user and purpose
        OtpCode::where('user_id', $user->id)
            ->where('purpose', 'biometric_enrollment')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['used_at' => now()]);

        // Generate 6-digit code
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'phone' => $phone,
            'purpose' => 'biometric_enrollment',
            'expires_at' => now()->addMinutes(5),
        ]);

        // Save phone to user profile if not already set
        if (!$user->phone) {
            $user->update(['phone' => $phone]);
        }

        // TODO: Replace with actual SMS gateway integration
        Log::info("OTP for user {$user->id} ({$phone}): {$code}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to ' . $phone,
            'debug_code' => config('app.env') === 'local' ? $code : null,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();
        $code = $request->code;

        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('purpose', 'biometric_enrollment')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        $otp->update(['used_at' => now()]);

        // Mark phone as verified
        $user->update(['phone_verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Phone verified successfully',
        ]);
    }
}
