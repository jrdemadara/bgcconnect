<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DigitalIdController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $idUrl = Storage::disk('s3')->exists('digitalid/' . $user->code . '.jpg')
        ? Storage::temporaryUrl('digitalid/' . $user->code . '.jpg', now()->addMinutes(60))
        : '';

        return Inertia::render('Profile/DigitalID', [
            'digital_id' => $idUrl,
        ]);
    }
}
