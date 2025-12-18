<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class ApiSliderController extends Controller
{
    /**
     * ===============================
     * GET ALL SLIDERS
     * ===============================
     */
    public function index(): JsonResponse
    {
        $sliders = Slider::with('extraImages')
            ->latest()
            ->get()
            ->map(fn ($item) => $this->formatSlider($item));

        return response()->json([
            'status'  => true,
            'message' => 'List of sliders',
            'data'    => $sliders,
        ]);
    }

    /**
     * ===============================
     * GET HOME SLIDERS ONLY
     * ===============================
     */
    public function showHomeSlider(): JsonResponse
    {
        $sliders = Slider::with('extraImages')
            ->where('display_on_home', true)
            ->latest()
            ->get()
            ->map(fn ($item) => $this->formatSlider($item));

        return response()->json([
            'status' => true,
            'data'   => $sliders,
        ]);
    }

    /**
     * ===============================
     * FORMAT SLIDER RESPONSE
     * ===============================
     */
    private function formatSlider($item): array
    {
        return [
            'id'              => $item->id,
            'title'           => $item->title,
            'subtitle'        => $item->subtitle,
            'youtube_id'      => $item->youtube_id,
            'button_text'     => $item->button_text,
            'url_link'        => $item->url_link,
            'display_on_home' => (bool) $item->display_on_home,

            // IMAGE UTAMA
            'image' => $item->image,
            'image_url' => $item->image
                ? asset('storage/' . $item->image)
                : null,

            // FOTO TAMBAHAN
            'extra_images' => $item->extraImages->map(fn ($img) => [
                'id'        => $img->id,
                'title'     => $img->title,
                'subtitle'  => $img->subtitle,
                'image'     => $img->image,
                'image_url' => $img->image
                    ? asset('storage/' . $img->image)
                    : null,
            ]),

            'created_at' => $item->created_at,
        ];
    }
}
