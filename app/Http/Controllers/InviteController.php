<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->query('code');

        return Inertia::render('Auth/Register', [
            'code' => $code,
        ]);
    }

}
