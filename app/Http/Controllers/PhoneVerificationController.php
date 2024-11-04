<?php

namespace App\Http\Controllers;

use App\Models\Settings;
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

        $settings = Settings::find(1);

        $last_channel = $settings->last_channel;
        $channel_size = $settings->channel_size;
        $verification_expiry = $settings->verification_expiry;

        // Generate a random verification code
        $verificationCode = $this->generateRandomString();

        // Store the verification code in Redis with an expiration of 1 hour
        Redis::setex("verification_code:{$user->id}", $verification_expiry, $verificationCode);

        // Determine the next channel (ensure it loops back to '1' after '5')
        $next_channel = ($last_channel % $channel_size) + 1; // Assuming you have 5 channels (sms1, sms2, ..., sms5)

        // Prepare and publish the message to the next SMS channel
        Redis::publish('sms' . $next_channel, json_encode([
            'phone_number' => $phone,
            'verification_code' => $verificationCode,
        ]));

        // Update the last channel in the database
        $settings->last_channel = $next_channel;
        $settings->save();

        return response()->json(['message' => 'sent'], 200);
    }

    public function verify(Request $request)
    {

        $settings = Settings::find(1);

        $multilevel_size = $settings->multilevel_size;
        $direct_points = $settings->direct_points;
        $downline_points = $settings->downline_points;

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

        $user->increment('points', 30);
        $user->transactions()->create([
            'points_earned' => 30,
            'description' => 'phone verification bonus',
        ]);

        // If user has referrer, create transaction
        $referrer = $user->referred_by;

        if ($referrer) {
            // distribute points
            $this->distributeReferralPoints($user, $multilevel_size, $direct_points, $downline_points);
        }

        // Delete the Redis key
        Redis::del($verificationCodeKey);

        return response()->json(['message' => 'Verification successful.'], 200);
    }

    public function distributeReferralPoints(User $newUser, int $multilevel_size, int $direct_points, int $downline_points)
    {
        // Get the direct referrer by their ID
        $directReferrerId = $newUser->referred_by;

        if ($directReferrerId) {
            // Find the referrer as a User model
            $directReferrer = User::find($directReferrerId);

            if ($directReferrer) {
                // Direct referral gets 10 points
                $this->awardPoints($directReferrer, $direct_points);

                // Recursively distribute points to the upline
                $this->distributeToUpline($directReferrer, 1, $multilevel_size, $direct_points, $downline_points);
            }
        }
    }

    public function distributeToUpline(User $user, int $level, int $multilevel_size, int $direct_points, int $downline_points)
    {
        // Stop at multilevel_size
        if ($level > $multilevel_size) {
            return;
        }

        // Get the user's referrer by their ID
        $uplineId = $user->referred_by;

        if ($uplineId) {
            // Find the upline as a User model
            $upline = User::find($uplineId);

            if ($upline) {
                // Award lower_level_points to the upline
                $this->awardPoints($upline, $downline_points);

                // Recursively distribute points to the next level
                $this->distributeToUpline($upline, $level + 1, $multilevel_size, $direct_points, $downline_points);
            }
        }
    }

    protected function awardPoints(User $user, int $points)
    {
        // You can either use transactions or create a separate points table to log transactions
        $user->increment('points', $points);

        // Optionally, log the transaction
        $user->transactions()->create([
            'points_earned' => $points,
            'description' => 'referral bonus',
        ]);
    }

    private function generateRandomString()
    {
        // Define your allowed characters (both letters and numbers)
        $characters = '0123456789';

        // Shuffle and pick random characters
        return substr(str_shuffle(str_repeat($characters, 8)), 0, 8);
    }
}
