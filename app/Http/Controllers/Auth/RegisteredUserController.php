<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Profile;
use App\Models\Referrals;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
            'code'      => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'phone'     => 'required|string|max:11|unique:users',
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if the referrer exists
        $referrer = User::where('code', $request->code)->pluck('id')->first();

        if (empty($referrer)) {
            return redirect()->back()->withErrors(['code' => 'Referrer not found. Please make sure the code is correct.']);
        }

        // Create the user
        $user = User::create([
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
            'code'        => $this->generateRandomString(),
            'referred_by' => $referrer,
            'points'      => 10, // Registration bonus points
        ]);

        // Log the transaction
        $user->transactions()->create([
            'points_earned' => 10,
            'description'   => 'Registration bonus',
        ]);

        // Create the referral record
        $referral              = new Referrals();
        $referral->referrer_id = $referrer;
        $referral->referred_id = $user->id;
        $referral->save();

        // Create the user profile
        $profile = Profile::create([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'user_id'   => $user->id,
        ]);

        // Trigger the registered event
        event(new Registered($user));

        Log::create([
            'type'       => 'info',
            'content'    => [
                'action'      => 'register',
                'user_id'     => $user->id,
                'name'        => $profile->lastname . ' ' . $profile->firstname,
                'phone'       => $user->phone,
                'referred_by' => $user->referred_by,
            ],
            'ip_address' => $request->ip(),
        ]);

        // Log in the user
        Auth::login($user);

        // Redirect to the profile page
        return redirect()->route('profile.index');
    }

    private function generateRandomString()
    {
        // Define your allowed characters (both letters and numbers)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        // Shuffle and pick random characters
        return substr(str_shuffle(str_repeat($characters, 12)), 0, 12);
    }
}
