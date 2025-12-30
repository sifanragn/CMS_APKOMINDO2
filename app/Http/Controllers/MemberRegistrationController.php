<?php

namespace App\Http\Controllers;

use App\Models\MemberRegistration;
use App\Models\Industry;
use App\Models\DpdLocation;
use Illuminate\Http\Request;

class MemberRegistrationController extends Controller
{
    public function index()
    {
        $data = MemberRegistration::with(['industry','dpd'])->latest()->get();
        return view('member_registrations.index', compact('data'));
    }

    public function show($id)
    {
        $item = MemberRegistration::with(['industry','dpd'])->findOrFail($id);
        $industries = Industry::where('is_active',1)->get();
        $dpds = DpdLocation::where('is_active',1)->get();

        return view('member_registrations.show', compact('item','industries','dpds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'last_name'            => 'required|string|max:255',
            'company'              => 'required|string|max:255',
            'primary_phone'        => 'required|string|max:50',
            'mobile_phone'         => 'required|string|max:50',
            'primary_email'        => 'required|email',
            'website'              => 'nullable|string|max:255',
            'street'               => 'required|string',
            'city'                 => 'required|string|max:255',
            'postal_code'          => 'required|string|max:20',
            'industry_id'          => 'nullable|exists:industries,id',
            'dpd_location_id'      => 'nullable|exists:dpd_locations,id',
            'annual_revenue'       => 'nullable|string|max:255',
            'number_of_employees'  => 'nullable|integer',
            'nib_siup'             => 'required|string|max:255',
            'npwp_usaha'           => 'required|string|max:255',
            'ktp_pic'              => 'nullable|string|max:255',
        ]);

        $data['status'] = 'pending';

        MemberRegistration::create($data);

        return redirect()->back()->with('success', 'Pendaftaran berhasil dikirim');
    }


    public function approve($id)
    {
        MemberRegistration::where('id',$id)->update(['status'=>'approved']);
        return back()->with('success','Approved');
    }

    public function reject(Request $request, $id)
    {
        MemberRegistration::where('id',$id)->update([
            'status'=>'rejected',
            'admin_note'=>$request->admin_note
        ]);

        return back()->with('success','Rejected');
    }
}

