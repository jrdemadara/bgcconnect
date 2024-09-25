<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Inertia\Response;

class PhoneVerificationController extends Controller
{

    public function index(): Response
    {
        // Render the response
        return Inertia::render('Profile/VerifyPhone');
    }

    public function send()
    {
        // Get the authenticated user's ID and phone number
        $user = Auth::user();

        $phone = $user->phone;

        // Generate a random verification code
        $verificationCode = $this->generateRandomString();

        // Store the verification code in Redis with an expiration of 1 hour
        Redis::setex("verification_code:{$user->id}", 3600, $verificationCode);

        // Prepare and publish the message to the SMS channel
        Redis::publish('sms', json_encode([
            'phone_number' => $phone,
            'verification_code' => $verificationCode,
        ]));

        return response()->json(['message' => 'sent'], 200);
    }

    public function verify(Request $request)
    {
        $id = Auth::id();
        $verificationCodeKey = "verification_code:$id";
        $verification_code = Redis::get($verificationCodeKey);

        // Validate the verification code
        if (!$verification_code || $request->verification_code !== $verification_code) {
            return response()->json(['error' => 'Invalid verification code.'], 400);
        }

        // Update user verification status
        $user = User::findOrFail($id);
        $user->update([
            'phone_verified_at' => now(),
            'level' => 3,
        ]);

        $referrer = $user->referred_by;

        if ($referrer) {
            // Create transaction
            $transaction = new Transaction();
            $transaction->user_id = $referrer;
            $transaction->points_earned = 10;
            $transaction->description = "invited user id " . $user->id;
            $transaction->save();

        }

        // Delete the Redis key
        Redis::del($verificationCodeKey);

        return response()->json(['message' => 'Verification successful.'], 200);
    }

    private function generateRandomString()
    {
        // Define your allowed characters (both letters and numbers)
        $characters = '0123456789';

        // Shuffle and pick random characters
        return substr(str_shuffle(str_repeat($characters, 8)), 0, 8);
    }
}
