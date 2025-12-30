<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    public function index()
    {
        $data = Industry::all();
        return view('industries.index', compact('data'));
    }

    public function store(Request $request)
    {
        Industry::create($request->validate([
            'name'=>'required'
        ]));

        return back();
    }

    public function update(Request $request, Industry $industry)
    {
        $industry->update($request->only('name','is_active'));
        return back();
    }

    public function destroy(Industry $industry)
    {
        $industry->delete();
        return back();
    }
}
