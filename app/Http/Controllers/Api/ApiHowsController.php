<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hows;
use Illuminate\Http\Request;

class ApiHowsController extends Controller
{
    /**
     * GET Semua langkah HOW IT WORKS
     */
    public function index()
    {
        try {
            $hows = Hows::orderBy('step_number')->get()
                ->map(function ($item) {
                    // Jika ada gambar, buat full URL
                    $item->image_url = $item->image 
                        ? asset('storage/' . $item->image)
                        : null;
                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'List of HOW steps',
                'data' => $hows
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data HOWs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET Detail step
     */
    public function show($id)
    {
        try {
            $how = Hows::find($id);

            if (!$how) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // Tambahkan URL gambar jika ada
            $how->image_url = $how->image 
                ? asset('storage/' . $how->image)
                : null;

            return response()->json([
                'status' => true,
                'message' => 'Detail step berhasil diambil',
                'data' => $how
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
