<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;

class ApiCareerController extends Controller
{
    /**
     * GET: Semua career
     */
    public function index()
    {
        try {
            $careers = Career::latest()->get()
                ->map(function ($item) {

                    // Tambah URL gambar kalau ada
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;

                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'List of careers',
                'data' => $careers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data career',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Detail career
     */
    public function show($id)
    {
        try {
            $career = Career::findOrFail($id);

            // Tambah URL gambar
            $career->image_url = $career->image
                ? asset('storage/' . $career->image)
                : null;

            return response()->json([
                'status' => true,
                'message' => 'Detail career berhasil diambil',
                'data' => $career
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Career tidak ditemukan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
