<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;

class ApiKegiatanController extends Controller
{
    /**
     * GET semua kegiatan
     */
    public function index()
    {
        try {
            $kegiatan = Kegiatan::with('category')
                ->latest()
                ->get()
                ->map(function ($item) {
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;

                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'Data Kegiatan berhasil diambil',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data Kegiatan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET detail kegiatan
     */
    public function show($id)
    {
        try {
            $kegiatan = Kegiatan::with('category')->find($id);

            if (!$kegiatan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'data' => null
                ], 404);
            }

            $kegiatan->image_url = $kegiatan->image
                ? asset('storage/' . $kegiatan->image)
                : null;

            return response()->json([
                'status' => true,
                'message' => 'Detail Kegiatan berhasil diambil',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil detail Kegiatan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET kegiatan berdasarkan kategori
     */
    public function byCategory($categoryId)
    {
        try {
            $kegiatan = Kegiatan::with('category')
                ->where('category_kegiatan_id', $categoryId)
                ->latest()
                ->get()
                ->map(function ($item) {
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;

                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'Data Kegiatan berdasarkan kategori berhasil diambil',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data berdasarkan kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
