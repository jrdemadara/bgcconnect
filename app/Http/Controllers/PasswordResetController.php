<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            $settings = Settings::find(1);
            $expiry = $settings->verification_expiry;

            $resetCodeKey = "reset_code:$user->id";
            $reset_code = Redis::get($resetCodeKey);

            if ($reset_code) {
                // Delete the Redis key
                Redis::del($resetCodeKey);
            }

            // Generate a random reset code
            $resetCode = $this->generateRandomString();

            // Store the reset code in Redis
            Redis::setex("reset_code:{$user->id}", $expiry, $resetCode);

            Redis::publish('sms6', json_encode([
                'phone' => $request->phone,
                'reset_code' => $resetCode,
            ]));

            return response()->json(['success' => $resetCode], 200);
        }

        return response()->json(['error' => 'phone not found'], 404);
    }

    public function checkResetCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            $resetCodeKey = "reset_code:$user->id";
            $reset_code = Redis::get($resetCodeKey);

            if (!$reset_code || $request->code !== $reset_code) {
                return response()->json(['error' => 'Invalid reset code.'], 400);
            }

            return response()->json(['success' => 'reset code matched.'], 200);
        }

        return response()->json(['error' => 'phone not found'], 404);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            $user->password = $request->password;
            $user->save();
            return response()->json(['success' => 'password reset success.'], 200);
        }

        return response()->json(['error' => 'phone not found'], 404);
    }

    private function generateRandomString()
    {
        // Define your allowed characters (both letters and numbers)
        $characters = '0123456789';

        // Shuffle and pick random characters
        return substr(str_shuffle(str_repeat($characters, 8)), 0, 8);
    }
}
