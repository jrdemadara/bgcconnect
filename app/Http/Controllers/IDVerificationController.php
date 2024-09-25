<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Get authenticated user's profile
        $profile = Profile::findOrFail(Auth::id());

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
        $user->id_check = true;
        $user->save();

        // Return response with uploaded file paths
        return response()->json([
            'front' => $profile->id_card_front,
            'back' => $profile->id_card_back,
        ], 200);
    }

}