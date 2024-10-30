<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DownlinesController extends Controller
{

    public function buildTree(array $elements, $parentId = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element->referred_by == $parentId) {
                // Recursively build the children
                $children = $this->buildTree($elements, $element->id);
                $element->children = $children; // Add children to the current element
                $element->name = $element->full_name; // Use full_name for the node name
                $branch[] = $element; // Add the current element to the branch
            }
        }
        return $branch;
    }

    public function index(Request $request): InertiaResponse
    {
        $user = Auth::user();

        $allDownline = DB::select('
        WITH RECURSIVE referral_hierarchy AS (
            SELECT u.id, u.referred_by
            FROM users u
            WHERE u.id = ?
            UNION ALL
            SELECT u.id, u.referred_by
            FROM users u
            INNER JOIN referral_hierarchy rh ON rh.id = u.referred_by
        )
        SELECT rh.id, rh.referred_by,
               CONCAT(p.lastname, " ", p.firstname) AS full_name
        FROM referral_hierarchy rh
        INNER JOIN profiles p ON rh.id = p.user_id
        WHERE rh.id != ?;
    ', [$user->id, $user->id]);

        // Build the tree structure
        $treeData = $this->buildTree($allDownline, $user->id);

        $result = json_encode([
            'children' => $treeData,
        ]);

        // Return the data to the view
        return Inertia::render('Profile/Downlines', [
            'data' => $result, // Send the structured data to the front end
        ]);
    }

}
