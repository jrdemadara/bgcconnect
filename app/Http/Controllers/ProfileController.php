<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{

    // Display the user's profile form.
    public function index(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->profile->select('firstname', 'lastname', 'avatar')->first();
        return Inertia::render('Profile/Profile', [
            // 'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    // Display the user's profile form for edit.
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->profile;
        return Inertia::render('Profile/Edit', [
            'status' => session('status'),
            'user' => $user,
            'profile' => $profile,
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $profile = Profile::where('id', $request->user()->id)->first();

        $profile->lastname = Str::lower($request->lastname);
        $profile->firstname = Str::lower($request->firstname);
        $profile->middlename = Str::lower($request->middlename);
        $profile->extension = Str::lower($request->extension);
        $profile->precinct_number = Str::lower($request->precinct_number);

        $profile->region = $request->region;
        $profile->province = $request->province;
        $profile->municipality_city = $request->municipality_city;
        $profile->barangay = $request->barangay;
        $profile->street = Str::lower($request->street);
        $profile->gender = Str::lower($request->gender);
        $profile->birthdate = $request->birthdate;
        $profile->civil_status = Str::lower($request->civil_status);
        $profile->blood_type = $request->blood_type;
        $profile->religion = Str::lower($request->religion);
        $profile->tribe = Str::lower($request->tribe);
        $profile->industry_sector = Str::lower($request->industry_sector);
        $profile->occupation = Str::lower($request->occupation);
        $profile->income_level = $request->income_level;
        $profile->affiliation = Str::lower($request->affiliation);
        $profile->facebook = Str::lower($request->facebook);
        $profile->user_id = $request->user()->id;
        $profile->save();

        $user = $request->user();
        $user->level = 2;
        $user->save();

        return Redirect::route('profile.edit');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'avatar' => 'required|file|mimes:jpeg,jpg|max:2048',
        ]);

        $id = Auth::id();

        $profile = Profile::where('id', $id)->first();

        // Store the file in S3 (automatically uses the 's3' disk)
        $path = $request->file('avatar')->store('users', 's3');

        if (!$path) {
            return response()->json(['error' => 'File upload failed'], 500);
        }

        $profile->avatar = $path;
        $profile->save();

        return response()->json(['success' => 'Upload success'], 200);

    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

}
