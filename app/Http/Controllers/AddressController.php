<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function getProvinces()
    {
        $data = Province::where('regCode', '1900000000')->get();

        return response()->json([
            'provinces' => $data,
        ]);
    }

    public function getMunicipalities(Request $request)
    {
        $data = Municipality::where('provCode', $request->province)->get();
        return response()->json([
            'municipalities' => $data,
        ]);
    }

    public function getBarangays(Request $request)
    {
        $data = Barangay::where('citymunCode', $request->municipality)->get();
        return response()->json([
            'barangays' => $data,
        ]);
    }
}
