<?php

namespace App\Http\Controllers;

use App\Models\ActivityAttendees;
use App\Models\Barangay;
use App\Models\Profile;
use App\Models\Province;
use App\Models\RaffleDraw;
use App\Models\Referrals;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{

    // Display the user's profile form.
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)
            ->select('firstname', 'lastname', 'avatar', 'province', 'municipality_city', 'barangay')
            ->first();

        // Fetch the latest raffle draw
        $draw = RaffleDraw::latest()->first();

        // Prepare the avatar URL
        $avatarUrl = $profile && $profile->avatar
        ? Storage::temporaryUrl($profile->avatar, now()->addMinutes(10))
        : '';

        $downline = User::where('referred_by', $user->id)->count();
        $allDownline = DB::select('
    WITH RECURSIVE referral_hierarchy AS (
        SELECT id, referred_by FROM users WHERE id = ?
        UNION ALL
        SELECT u.id, u.referred_by
        FROM users u
        INNER JOIN referral_hierarchy rh ON rh.id = u.referred_by
    )
    SELECT COUNT(*) AS downline_count
    FROM referral_hierarchy
    WHERE id != ?;
', [$user->id, $user->id]);

        $activitiesCount = ActivityAttendees::where('user_id', $user->id)->count();

        return Inertia::render('Profile/Profile', [
            'status' => session('status'),
            'avatar' => $avatarUrl,
            'profile' => $profile,
            'draw' => $draw ? $draw->draw_date : null,
            'downline' => $downline,
            'all_downline' => !empty($allDownline) ? $allDownline[0]->downline_count : 0,
            'activities' => $activitiesCount,
            'points_comparison' => $this->getPointsChange($user->id),
            'referral_comparison' => $this->getReferralChange($user->id),
            'activity_comparison' => $this->getActivityChange($user->id),
            'downlines_comparison' => $this->getDownlineChange($user->id),
        ]);
    }

    public function getUser()
    {
        $user = Auth::user();
        $firstname = Profile::where('user_id', $user->id)
            ->pluck('firstname')
            ->first();

        $drawExists = RaffleDraw::latest()->exists();

        return response()->json([
            'user_code' => $user->code,
            'level' => $user->level,
            'firstname' => $firstname,
            'draw_date' => $drawExists,

        ]);
    }

    // Display the user's profile form for edit.
    public function edit(Request $request): Response
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Use an empty object as a fallback if the profile is null
        $profile = $profile ?? (object) [
            'province' => null,
            'municipality_city' => null,
            'barangay' => null,
        ];

        // Render the view, ensuring that we handle potential null values
        return Inertia::render('Profile/Edit', [
            'status' => session('status'),
            'user' => $user,
            'profile' => $profile,
            'province' => $profile->province ?? '', // Safely access description or return empty
            'municipality' => $profile->municipality_city ?? '',
            'barangay' => $profile->barangay ?? '',
        ]);

    }
    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {

        $request->validate([
            'lastname' => 'required|string|max:191',
            'firstname' => 'required|string|max:191',
            'middlename' => 'required|string|max:191',
            'province' => 'required|string|max:191',
            'municipality_city' => 'required|string|max:191',
            'barangay' => 'required|string|max:191',
            'street' => 'required|string|max:191',
            'gender' => 'required|string|max:191',
            'birthdate' => 'required|string|max:191',
            'civil_status' => 'required|string|max:191',
            'blood_type' => 'required|string|max:191',
            'religion' => 'required|string|max:191',
            'tribe' => 'required|string|max:191',
            'livelihood' => 'required|string|max:191',

            'industry_sector' => 'nullable|string|max:191',
            'occupation' => 'nullable|string|max:191',
            'income_level' => 'nullable|string|max:191',

            'position' => 'required|string|max:191',
            'affiliation' => 'required|string|max:191',
            'facebook' => 'required|string|max:191',
        ]);

        $id = Auth::id();
        $user = User::findOrFail($id);

        $profile = $user->profile;

        if ($user->level < 4) {
            $profile->lastname = Str::lower($request->lastname);
            $profile->firstname = Str::lower($request->firstname);
            $profile->middlename = Str::lower($request->middlename);
            $profile->extension = $request->extension ? Str::lower($request->extension) : "";
            $profile->precinct_number = Str::lower($request->precinct_number);

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
            $profile->livelihood = Str::lower($request->livelihood);

            $profile->industry_sector = Str::lower($request->industry_sector);
            $profile->occupation = Str::lower($request->occupation);
            $profile->income_level = $request->income_level;

            $profile->position = Str::lower($request->position);
            $profile->affiliation = Str::lower($request->affiliation);
            $profile->facebook = Str::lower($request->facebook);
            $profile->user_id = $id;
            $profile->save();

            if ($user->level < 2) {
                $user->level = 2;
                $user->increment('points', 20);
                $user->save();

                $user->transactions()->create([
                    'points_earned' => 20,
                    'description' => 'profile completion bonus',
                ]);

            }
        }

        return redirect(route('profile.edit'));
    }

    public function updatePhoto(Request $request)
    {
        // Validate request inputs
        $request->validate([
            'avatar' => 'required|file|mimes:jpeg,jpg|max:2048',
        ]);

        $id = Auth::id();

        // Fetch the user's profile
        $profile = Profile::findOrFail($id); // Use findOrFail for better error handling

        // Delete existing avatar if it exists
        if ($profile->avatar && Storage::disk('s3')->exists($profile->avatar)) {
            Storage::disk('s3')->delete($profile->avatar);
        }

        // Store the new avatar in S3
        $path = $request->file('avatar')->store('users');

        // Check if the file upload was successful
        if (!$path) {
            return response()->json(['error' => 'File upload failed'], 500);
        }

        // Update the profile with the new avatar path
        $profile->avatar = $path;
        $profile->save();

        return response()->json(['success' => 'Upload successful'], 200);
    }

    public function updateSignature(Request $request)
    {
        // Validate request inputs
        $request->validate([
            'signature' => 'required|file|mimes:png|max:2048',
        ]);

        $id = Auth::id();

        // Fetch the user's profile
        $profile = Profile::findOrFail($id); // Use findOrFail for better error handling

        // Delete existing avatar if it exists
        if ($profile->esig && Storage::disk('s3')->exists($profile->esig)) {
            Storage::disk('s3')->delete($profile->esig);
        }

        // Store the new avatar in S3
        $path = $request->file('signature')->store('signature');

        // Check if the file upload was successful
        if (!$path) {
            return response()->json(['error' => 'File upload failed'], 500);
        }

        // Update the profile with the new avatar path
        $profile->esig = $path;
        $profile->save();

        return response()->json(['success' => 'Upload successful'], 200);
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

    public function getPointsChange($userId)
    {
        // Get the current date and the first and last day of the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Get the first and last day of the previous month
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Retrieve total points for the current month
        $currentMonthPoints = Transaction::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('points_earned');

        // Retrieve total points for the previous month
        $lastMonthPoints = Transaction::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('points_earned');

        // Calculate percentage change
        if ($lastMonthPoints == 0) {
            // Avoid division by zero, if no points were earned last month, assume 100% increase
            $percentageChange = $currentMonthPoints > 0 ? 100 : 0;
        } else {
            $percentageChange = (($currentMonthPoints - $lastMonthPoints) / $lastMonthPoints) * 100;
        }

        // Format the percentage change with + or - sign
        $sign = $percentageChange >= 0 ? '+' : '-';
        $percentageChange = abs($percentageChange); // Convert to positive for display

        // Return the result as a string with + or - sign
        return $sign . number_format($percentageChange, 0);
    }

    public function getReferralChange($userId)
    {
        // Get the current date and the first and last day of the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Get the first and last day of the previous month
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Retrieve total points for the current month
        $currentMonth = Referrals::where('referrer_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Retrieve total points for the previous month
        $lastMonth = Referrals::where('referrer_id', $userId)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        // Calculate percentage change
        if ($lastMonth == 0) {
            // Avoid division by zero, if no points were earned last month, assume 100% increase
            $percentageChange = $currentMonth > 0 ? 100 : 0;
        } else {
            $percentageChange = (($currentMonth - $lastMonth) / $lastMonth) * 100;
        }

        // Format the percentage change with + or - sign
        $sign = $percentageChange >= 0 ? '+' : '-';
        $percentageChange = abs($percentageChange); // Convert to positive for display

        // Return the result as a string with + or - sign
        return $sign . number_format($percentageChange, 0);
    }

    public function getActivityChange($userId)
    {
        // Get the current date and the first and last day of the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Get the first and last day of the previous month
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Retrieve total points for the current month
        $currentMonth = ActivityAttendees::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Retrieve total points for the previous month
        $lastMonth = ActivityAttendees::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        // Calculate percentage change
        if ($lastMonth == 0) {
            // Avoid division by zero, if no points were earned last month, assume 100% increase
            $percentageChange = $currentMonth > 0 ? 100 : 0;
        } else {
            $percentageChange = (($currentMonth - $lastMonth) / $lastMonth) * 100;
        }

        // Format the percentage change with + or - sign
        $sign = $percentageChange >= 0 ? '+' : '-';
        $percentageChange = abs($percentageChange); // Convert to positive for display

        // Return the result as a string with + or - sign
        return $sign . number_format($percentageChange, 0);
    }

    public function getDownlineChange($userId)
    {
        $currentMonthCount = DB::select('
        WITH RECURSIVE referral_hierarchy AS (
            SELECT id, referred_by, created_at
            FROM users
            WHERE referred_by = ?
            UNION ALL
            SELECT u.id, u.referred_by, u.created_at
            FROM users u
            INNER JOIN referral_hierarchy rh ON rh.id = u.referred_by
        )
        SELECT COUNT(*) AS current_month_count
        FROM referral_hierarchy
        WHERE created_at >= DATE_FORMAT(NOW() ,\'%Y-%m-01\')
    ', [$userId]);

        $previousMonthCount = DB::select('
        WITH RECURSIVE referral_hierarchy AS (
            SELECT id, referred_by, created_at
            FROM users
            WHERE referred_by = ?
            UNION ALL
            SELECT u.id, u.referred_by, u.created_at
            FROM users u
            INNER JOIN referral_hierarchy rh ON rh.id = u.referred_by
        )
        SELECT COUNT(*) AS previous_month_count
        FROM referral_hierarchy
        WHERE created_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH ,\'%Y-%m-01\')
        AND created_at < DATE_FORMAT(NOW() ,\'%Y-%m-01\')
    ', [$userId]);

        $currentCount = $currentMonthCount[0]->current_month_count ?? 0;
        $previousCount = $previousMonthCount[0]->previous_month_count ?? 0;

        // Ensure both counts are integers
        $currentCount = intval($currentCount);
        $previousCount = intval($previousCount);

        // Calculate percentage change
        if ($previousCount == 0) {
            if ($currentCount > 0) {
                $percentageChange = 100; // 100% increase if previous count is 0 and current count > 0
            } else {
                $percentageChange = 0; // No change if both counts are 0
            }
        } else {
            // Ensure the division is safe
            $percentageChange = (($currentCount - $previousCount) / $previousCount) * 100;
        }

        // Format the percentage change
        $sign = $percentageChange >= 0 ? '+' : '-';
        $percentageChange = abs($percentageChange); // Get the absolute value for display

        return $sign . number_format($percentageChange, 0); // Return the result
    }

}
