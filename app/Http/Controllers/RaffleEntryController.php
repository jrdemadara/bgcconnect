<?php

namespace App\Http\Controllers;

use App\Models\RaffleDraw;
use App\Models\RaffleEntry;
use App\Models\RaffleTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RaffleEntryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $drawDate = RaffleDraw::latest()->first();
        $tier = RaffleTier::with('prizes')->get();
        $entries = RaffleEntry::where('user_id', $user->id)->count();
        return Inertia::render('Profile/RaffleEntry', [
            'tier' => $tier,
            'entries' => $entries,
            'draw_date' => $drawDate ? $drawDate->draw_date : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tier' => 'required',
        ]);

        $user = $request->user();

        $draw = RaffleDraw::all()->first();
        $tier = RaffleTier::find($request->tier);

        if ($user->level > 3) {
            $entry = new RaffleEntry();
            $entry->user_id = $user->id;
            $entry->tier_id = $tier->id;
            $entry->draw_id = $draw->id;
            $entry->save();

            if ($entry->wasRecentlyCreated) {
                $user->decrement('points', $tier->points);
            }

            return response()->json(['success' => 'success'], 200);
        }
        return response()->json(['error' => 'level is not enough'], 400);

    }

}
