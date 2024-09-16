<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $profile = $user->profile; // Fetch the user's profile
        $provinces = Province::select('provCode','provDescription')->where('regCode', '1900000000');
        return Inertia::render('Profile/Edit', [
            // 'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'profile' => $profile,
            'provinces' => $provinces,
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $profile = Profile::find($request->user('id'));
        $profile->lastname = Str::lower($request->lastname);
        $profile->firstname = Str::lower($request->firstname);
        $profile->middlename = Str::lower($request->middlename);
        $profile->extension = Str::lower($request->extension);
        $profile->precinct_number = Str::lower($request->precinct_number);
        $profile->avatar = Str::lower($request->avatar);
        $profile->id_type = Str::lower($request->id_type);
        $profile->id_card_front = Str::lower($request->id_card_front);
        $profile->id_card_back = Str::lower($request->id_card_back);
        $profile->region = Str::lower($request->region);
        $profile->province = Str::lower($request->province);
        $profile->municipality_city = Str::lower($request->municipality_city);
        $profile->barangay = Str::lower($request->barangay);
        $profile->street = Str::lower($request->street);
        $profile->gender = Str::lower($request->gender);
        $profile->birthdate = $request->birthdate;
        $profile->civil_status = Str::lower($request->civil_status);
        $profile->blood_type = Str::lower($request->blood_type);
        $profile->religion = Str::lower($request->religion);
        $profile->tribe = Str::lower($request->tribe);
        $profile->industry_sector = Str::lower($request->industry_sector);
        $profile->occupation = Str::lower($request->occupation);
        $profile->income_level = $request->income_level;
        $profile->affiliation = Str::lower($request->affiliation);
        $profile->facebook = Str::lower($request->facebook);
        $profile->user_id = $request->input('user');
        $profile->save();

        return Redirect::route('profile.edit');
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
