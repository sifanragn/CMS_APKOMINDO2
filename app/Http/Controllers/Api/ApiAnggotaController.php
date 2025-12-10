<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;

class ApiAnggotaController extends Controller
{
    public function index()
    {
        try {
            $anggota = Anggota::with('category')
                ->latest()
                ->get()
                ->map(function ($item) {

                    // Tambahkan URL gambar jika ada
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;

                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'Data Anggota berhasil diambil',
                'data' => $anggota
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
