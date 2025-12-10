<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tentangkami;
use App\Models\TentangkamiCategory;
use Illuminate\Support\Facades\Log;

class ApiTentangkamiController extends Controller
{
    // ===============================
    // GET Semua Data Tentang Kami
    // ===============================
    public function index()
    {
        try {
            $tentangkami = Tentangkami::with('category')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;
                    return $item;
                });

            return response()->json([
                'success' => true,
                'data' => $tentangkami
            ]);
        } catch (\Exception $e) {
            Log::error('API Tentangkami@index: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data'], 500);
        }
    }

    // ======================================
    // GET Tentangkami berdasarkan category_id
    // ======================================
    public function getByCategory($categoryId)
    {
        try {
            $tentangkami = Tentangkami::with('category')
                ->where('category_tentangkami_id', $categoryId)
                ->latest()
                ->get()
                ->map(function ($item) {
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;
                    return $item;
                });

            return response()->json(['success' => true, 'data' => $tentangkami]);
        } catch (\Exception $e) {
            Log::error('API Tentangkami@getByCategory: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data'], 500);
        }
    }

    // ===================================================
    // GET Tentangkami berdasarkan NAMA kategori (string)
    // ===================================================
    public function getByCategoryName($categoryName)
    {
        try {
            $category = TentangkamiCategory::where('name', $categoryName)->first();

            if (!$category) {
                return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
            }

            $tentangkami = Tentangkami::with('category')
                ->where('category_tentangkami_id', $category->id)
                ->latest()
                ->get()
                ->map(function ($item) {
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;
                    return $item;
                });

            return response()->json(['success' => true, 'data' => $tentangkami]);
        } catch (\Exception $e) {
            Log::error('API Tentangkami@getByCategoryName: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data'], 500);
        }
    }

    // ==========================================================
    // GET Semua Tentangkami yang tampil di Homepage (display_on_home = 1)
    // ==========================================================
    public function getDisplayOnHome()
    {
        try {
            $tentangkami = Tentangkami::with('category')
                ->where('display_on_home', true)
                ->latest()
                ->get()
                ->map(function ($item) {
                    $item->image_url = $item->image
                        ? asset('storage/' . $item->image)
                        : null;
                    return $item;
                });

            return response()->json(['success' => true, 'data' => $tentangkami]);
        } catch (\Exception $e) {
            Log::error('API Tentangkami@getDisplayOnHome: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data'], 500);
        }
    }
}
