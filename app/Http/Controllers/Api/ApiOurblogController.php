<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ourblog;
use Illuminate\Http\Request;

class ApiOurblogController extends Controller
{

    public function index()
    {
        $ourblogs = Ourblog::with('category')->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Data OurBlog berhasil diambil',
            'data' => $ourblogs
        ]);
    }

    public function show($id)
    {
        $ourblog = Ourblog::with('category')->find($id);

        if (!$ourblog) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail OurBlog berhasil diambil',
            'data' => $ourblog
        ]);
    }
}
