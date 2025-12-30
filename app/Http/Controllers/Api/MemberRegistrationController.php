<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use Illuminate\Http\Request;

class MemberRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'last_name' => 'required',
            'company' => 'required',
            'primary_phone' => 'required',
            'mobile_phone' => 'required',
            'primary_email' => 'required|email',
            'street' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'industry_id' => 'nullable|exists:industries,id',
            'dpd_location_id' => 'nullable|exists:dpd_locations,id',
            'nib_siup' => 'required',
            'npwp_usaha' => 'required',
            'ktp_pic' => 'nullable|file|max:5120'
        ]);

        if ($request->hasFile('ktp_pic')) {
            $data['ktp_pic'] = $request->file('ktp_pic')->store('ktp', 'public');
        }

        MemberRegistration::create($data);

        return response()->json([
            'message' => 'Pendaftaran anggota berhasil dikirim'
        ]);
    }
}

