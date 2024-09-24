<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->validate([
            'idPhotoFront' => 'required|file|mimes:jpeg,jpg|max:5000', // You can customize this validation
            'idPhotoBack' => 'required|file|mimes:jpeg,jpg|max:5000', // You can customize this validation
        ]);

        $id = Auth::id();

        $profile = Profile::where('id', $id)->first();

        // Store the file in S3 (automatically uses the 's3' disk)
        $front = $request->file('idPhotoFront')->store('idfront', 's3');
        $back = $request->file('idPhotoBack')->store('idback', 's3');

        if (!$front && !$back) {
            return response()->json(['error' => 'File upload failed'], 500);
        }

        $profile->id_card_front = $front;
        $profile->id_card_back = $back;
        $profile->save();

        return response()->json(['front' => $front, 'back' => $back], 200);

    }
}
