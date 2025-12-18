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
            $ourblogs = Ourblog::with(['category', 'extraImages'])
                ->latest()
                ->get()
                ->map(function ($item) {

                    return [
                        'id'          => $item->id,
                        'title'       => $item->title,
                        'slug'        => $item->slug ?? null,
                        'description' => $item->description,
                        'created_at'  => $item->created_at,
                        'category'    => $item->category,

                        // 🔥 IMAGE UTAMA
                        'image' => $item->image,
                        'image_url' => $item->image
                            ? asset('storage/' . $item->image)
                            : null,

                        // 🔥 FOTO TAMBAHAN
                        'extra_images' => $item->extraImages->map(function ($img) {
                            return [
                                'id'        => $img->id,
                                'title'     => $img->title,
                                'subtitle'  => $img->subtitle,
                                'image'     => $img->image,
                                'image_url' => asset('storage/' . $img->image),
                            ];
                        }),
                    ];
                });

            return response()->json([
                'status'  => true,
                'message' => 'Data OurBlog berhasil diambil',
                'data'    => $ourblogs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil data OurBlog',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET Detail blog
     */
    public function show($id)
    {
        try {
            $ourblog = Ourblog::with(['category', 'extraImages'])->find($id);

            if (!$ourblog) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan',
                    'data'    => null
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Detail OurBlog berhasil diambil',
                'data'    => [
                    'id'          => $ourblog->id,
                    'title'       => $ourblog->title,
                    'slug'        => $ourblog->slug ?? null,
                    'description' => $ourblog->description,
                    'created_at'  => $ourblog->created_at,
                    'category'    => $ourblog->category,

                    // IMAGE UTAMA
                    'image' => $ourblog->image,
                    'image_url' => $ourblog->image
                        ? asset('storage/' . $ourblog->image)
                        : null,

                    // FOTO TAMBAHAN
                    'extra_images' => $ourblog->extraImages->map(function ($img) {
                        return [
                            'id'        => $img->id,
                            'title'     => $img->title,
                            'subtitle'  => $img->subtitle,
                            'image'     => $img->image,
                            'image_url' => asset('storage/' . $img->image),
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
