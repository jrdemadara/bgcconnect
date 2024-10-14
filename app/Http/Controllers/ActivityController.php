<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\ActivityAttendees;
use App\Models\User;
use function Pest\Laravel\json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        return Inertia::render('Activity', [
            'isPhoneVerified' => $user->phone_verified_at ?: true, false,
            'status' => session('status'),
        ]);
    }

    public function checkLocation(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'code' => 'required|string',
        ]);

        $activity = Activities::where('code', $request->code)
            ->whereDate('date_start', '<=', now())
            ->whereDate('date_end', '>=', now())
            ->first();

        if ($activity) {
            $location = json_decode($activity->location, true);
            $radius = $location['Radius']; // Radius in meters
            $centerLatitude = $location['Latitude'];
            $centerLongitude = $location['Longitude'];

            return response()->json([
                'radius' => $radius,
                'centerLatitude' => $centerLatitude,
                'centerLongitude' => $centerLongitude,
            ], 200);
        }
        return response()->json([
            'message' => 'activity not found',
        ], 404);
    }

    public function storeActivityAttendees(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'code' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // Get the authenticated user's ID
        $id = Auth::id();

        $activity = Activities::where('code', $request->code)->first();

        // Check if the user has already attended this activity at current day
        $isAttended = ActivityAttendees::where('user_id', $id)
            ->where('activity_id', $activity->id)
            ->whereDate('created_at', '=', now())
            ->exists();

        if (!$isAttended) {
            // Add the user to the activity attendees
            ActivityAttendees::create([
                'activity_id' => $activity->id,
                'user_id' => $id,
            ]);

            // Increment the user's points
            $user = User::find($id);
            $user->increment('points', $activity->points); // Adds 10 points to the existing value

            return response()->json([
                'message' => 'success',
            ], 200);
        } else {
            return response()->json([
                'message' => 'already attended',
            ], 401);
        }

        // If the activity is not found or the date range does not match
        return response()->json([
            'message' => 'activity not found',
        ], 404);
    }
}
