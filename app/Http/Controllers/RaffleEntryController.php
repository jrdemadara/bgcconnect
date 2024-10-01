<?php

namespace App\Http\Controllers;

use App\Models\RaffleTier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RaffleEntryController extends Controller
{
    public function index(Request $request)
    {
        $tier = RaffleTier::with('prizes')->get();
        return Inertia::render('Profile/RaffleEntry', [
            'tier' => $tier,
        ]);
    }

}
