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
        $user = $request->user();
        return Inertia::render('Activity', [
            'isPhoneVerified' => $user->phone_verified_at ?: true, false,
            'status' => session('status'),
        ]);
    }

    public function storeActivityAttendees(Request $request)
    {
        $user = Auth::user();
        $activity = Activities::where('code', $request->qrcode);
        if ($activity) {
            ActivityAttendees::inserOrIgnore([
                'user_id' => $user,
            ], [
                'activity_id' => $activity->id(),
            ]);

            return response()->json([
                'data' => $activity,
            ], 200);

        }

        return response(404);
    }
}
