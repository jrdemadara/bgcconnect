<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function getMunicipality(Request $request)
    {
        $municipality = Municipality::select('citymunCode', 'citymunDescription')->where('provCode', $request->province);
        return redirect()->back()->with('municipality',  $municipality); 
    }
    public function getBarangay(Request $request)
    {
        $barangay = Municipality::select('brgyCode', 'brgyDescription')->where('citymunCode', $request->municipality);
        return redirect()->back()->with('barangay',  $barangay); 
    }
}
