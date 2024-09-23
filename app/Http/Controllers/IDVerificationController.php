<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
