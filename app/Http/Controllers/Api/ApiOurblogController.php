<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ourblog;

class ApiOurblogController extends Controller
{
    /**
     * GET semua blog
     */
    public function index()
    {
        try {
            $ourblogs = Ourblog::with('category')
                ->latest()
                ->get()
                ->map(function ($item) {

                    // Tambahkan URL gambar lengkap
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;

                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'Data OurBlog berhasil diambil',
                'data' => $ourblogs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data OurBlog',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET Detail blog
     */
    public function show($id)
    {
        try {
            $ourblog = Ourblog::with('category')->find($id);

            if (!$ourblog) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Tambah URL gambar lengkap
            $ourblog->image_url = $ourblog->image
                ? asset('storage/' . $ourblog->image)
                : null;

            return response()->json([
                'status' => true,
                'message' => 'Detail OurBlog berhasil diambil',
                'data' => $ourblog
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
