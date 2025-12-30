<?php

namespace App\Http\Controllers;

use App\Models\DpdLocation;
use Illuminate\Http\Request;

class DpdLocationController extends Controller
{
    public function index()
    {
        $data = DpdLocation::all();
        return view('dpd_locations.index', compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        DpdLocation::create([
            'name'  => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => 1,
        ]);

        return back()->with('success', 'DPD berhasil ditambahkan');
    }

    public function update(Request $request, DpdLocation $dpdLocation)
    {
        $dpdLocation->update($request->only('name','email','phone','is_active'));
        return back();
    }

    public function destroy(DpdLocation $dpdLocation)
    {
        $dpdLocation->delete();
        return back();
    }
}
