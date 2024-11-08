<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IDVerificationController extends Controller
{
    public function index(): Response
    {
        // Render the response
        return Inertia::render('Profile/VerifyID');
    }

    public function store(Request $request)
    {
        // Validate request inputs
        $request->validate([
            'idType' => 'required|string',
            'idPhotoFront' => 'required|file|mimes:jpeg,jpg|max:5000',
            'idPhotoBack' => 'required|file|mimes:jpeg,jpg|max:5000',
        ]);

        $id = Auth::id();
        $user = User::findOrFail($id);

        // Get authenticated user's profile
        $profile = Profile::findOrFail(Auth::id());

        if ($user->level == 3) {
            // Delete existing ID photos from S3 if they exist
            foreach (['id_card_front', 'id_card_back'] as $card) {
                // Check if the property is not null before calling exists
                if ($profile->$card && Storage::disk('s3')->exists($profile->$card)) {
                    Storage::disk('s3')->delete($profile->$card);
                }
            }

            // Store new ID photos in S3
            $profile->id_card_front = $request->file('idPhotoFront')->store('idfront', 's3');
            $profile->id_card_back = $request->file('idPhotoBack')->store('idback', 's3');

            // Check if file uploads were successful
            if (!$profile->id_card_front || !$profile->id_card_back) {
                return response()->json(['error' => 'File upload failed'], 500);
            }

            // Update profile with new data
            $profile->id_type = $request->idType;
            $profile->save();

            // Update user ID check status
            $user = User::findOrFail(Auth::id());
            $user->id_status = 1;
            $user->save();

            Redis::publish('sms0', json_encode([
                'id' => $user->id,
            ]));

            // Return response with uploaded file paths
            return response()->json([
                'front' => $profile->id_card_front,
                'back' => $profile->id_card_back,
            ], 200);

        } else {
            return response()->json(['error' => 'unauthorized'], 401);

        }

    }

    public function getIDPhoto(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $user = $request->user();
        $profile = $user->profile->select('avatar', 'id_card_front', 'id_card_back')->first();

        $avatarUrl = Storage::temporaryUrl($profile->avatar, now()->addMinutes(10));
        $idFrontUrl = Storage::temporaryUrl($profile->id_card_front, now()->addMinutes(10));
        $idBackUrl = Storage::temporaryUrl($profile->id_card_back, now()->addMinutes(10));

        return response()->json([
            'avatar' => $avatarUrl,
            'id_front' => $idFrontUrl,
            'id_back' => $idBackUrl,
        ], 200);
    }

}
