<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class QRCodeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Profile/QRCode', [
            'qrcode' => $user->code,
        ]);
    }
}
