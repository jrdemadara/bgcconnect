<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Referrals;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:11|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'code' => 'required|string',
        ]);

        $referrer = User::where('code', $request->code)->pluck('id')->first();

        $user = User::create([
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'code' => $this->generateRandomString(),
            'referred_by' => $referrer,

        ]);

        if ($referrer) {
            // Create referral
            $referral = new Referrals();
            $referral->referrer_id = $referrer;
            $referral->referred_id = $user->id;
            $referral->save();

        }

        Profile::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function generateRandomString()
    {
        // Define your allowed characters (both letters and numbers)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        // Shuffle and pick random characters
        return substr(str_shuffle(str_repeat($characters, 12)), 0, 12);
    }

}
