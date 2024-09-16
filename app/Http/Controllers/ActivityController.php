<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
}
