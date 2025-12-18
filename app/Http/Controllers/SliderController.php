<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\SliderImage; // ✅ MODEL YANG BENAR
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::with('extraImages')->latest()->get();
        return view('slider.index', compact('sliders'));
    }


    /**
     * Simpan slider baru
     */
   public function store(Request $request)
{
    Log::info($request->allFiles());

    DB::beginTransaction();

    try {
        // ===============================
        // VALIDATION
        // ===============================
        $validated = $request->validate([
            'title'           => 'nullable|string|max:255',
            'subtitle'        => 'nullable|string',
            'image'           => 'nullable|image|max:2048',
            'youtube_id'      => 'nullable|string|max:255',
            'button_text'     => 'nullable|string|max:100',
            'url_link'        => 'nullable|url|max:255',
            'display_on_home' => 'nullable|boolean',
        ]);

        // ===============================
        // IMAGE UTAMA
        // ===============================
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['display_on_home'] = $request->has('display_on_home') ? 1 : 0;

        // ===============================
        // CREATE SLIDER
        // ===============================
        $slider = Slider::create($validated);

        // ===============================
        // EXTRA IMAGES (FINAL FIX)
        // ===============================
        $allFiles = $request->allFiles();

        if (isset($allFiles['extra_images'])) {
            foreach ($allFiles['extra_images'] as $index => $files) {

                // pastikan array
                $files = is_array($files) ? $files : [$files];

                foreach ($files as $file) {
                    if (!$file instanceof \Illuminate\Http\UploadedFile) continue;

                    $path = $file->store('sliders/extra', 'public');

                    SliderImage::create([
                        'slider_id' => $slider->id,
                        'image'     => $path,
                        'title'     => $request->extra_titles[$index] ?? null,
                        'subtitle'  => $request->extra_subtitles[$index] ?? null,
                    ]);
                }
            }
        }

        DB::commit();

        return response()->json([
            'success'   => true,
            'slider_id' => $slider->id,
            'message'   => 'Slider berhasil ditambahkan'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('SLIDER STORE ERROR', [
            'msg' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Update slider
     */
public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $slider = Slider::findOrFail($id);

        /* ===============================
           VALIDATION
        =============================== */
        $validated = $request->validate([
            'title'           => 'nullable|string|max:255',
            'subtitle'        => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'youtube_id'      => 'nullable|string|max:255',
            'button_text'     => 'nullable|string|max:100',
            'url_link'        => 'nullable|url|max:255',
            'display_on_home' => 'nullable|boolean',
        ]);

        /* ===============================
           UPDATE DATA UTAMA SLIDER 🔥
        =============================== */
        if ($request->hasFile('image')) {
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['display_on_home'] = $request->has('display_on_home') ? 1 : 0;

        // 🔥 INI YANG SEBELUMNYA HILANG
        $slider->update($validated);
        /* ===============================
   ❌ DELETE EXTRA IMAGES
=============================== */
if ($request->filled('delete_extra_ids')) {
    foreach ($request->delete_extra_ids as $imgId) {

        $img = SliderImage::find($imgId);
        if (!$img) continue;

        // hapus file fisik
        if ($img->image && Storage::disk('public')->exists($img->image)) {
            Storage::disk('public')->delete($img->image);
        }

        // hapus record DB
        $img->delete();
    }
}


       /* ===============================
   HANDLE EXTRA IMAGES (FIX FINAL)
=============================== */

// 1️⃣ UPDATE TITLE & SUBTITLE (WALAU TANPA FILE)
if ($request->has('extra_titles') || $request->has('extra_subtitles')) {
    foreach ($request->extra_titles ?? [] as $key => $title) {

        if (!is_numeric($key)) continue;

        $img = SliderImage::find($key);
        if (!$img) continue;

        $img->update([
            'title'    => $title,
            'subtitle' => $request->extra_subtitles[$key] ?? $img->subtitle,
        ]);
    }
}

// 2️⃣ HANDLE FILE IMAGE (JIKA ADA)
if ($request->hasFile('extra_images')) {
    foreach ($request->file('extra_images') as $key => $file) {

        // ➕ FOTO BARU
        if (str_starts_with($key, 'new')) {
            if (!$file instanceof \Illuminate\Http\UploadedFile) continue;

            $path = $file->store('sliders/extra', 'public');

            SliderImage::create([
                'slider_id' => $slider->id,
                'image'     => $path,
                'title'     => $request->extra_titles[$key] ?? null,
                'subtitle'  => $request->extra_subtitles[$key] ?? null,
            ]);
        }

        // 🔁 REPLACE FOTO LAMA
        if (is_numeric($key) && $file instanceof \Illuminate\Http\UploadedFile) {
            $img = SliderImage::find($key);
            if (!$img) continue;

            if ($img->image && Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }

            $img->update([
                'image' => $file->store('sliders/extra', 'public'),
            ]);
        }
    }
}

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Slider berhasil diperbarui.'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


    public function show($id)
{
    $slider = Slider::with('extraImages')->findOrFail($id);

    return view('slider.show', compact('slider'));
}



    public function destroy(Request $request, $id)
    {
        try {
            $slider = Slider::findOrFail($id);

            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            foreach ($slider->extraImages as $img) {
                if (Storage::disk('public')->exists($img->image)) {
                    Storage::disk('public')->delete($img->image);
                }
                $img->delete();
            }

            $slider->delete();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slider berhasil dihapus.'
                ]);
            }

            return redirect()->route('slider.index')->with('success', 'Slider berhasil dihapus.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function toggleDisplay($id)
    {
        $slider = Slider::findOrFail($id);
        $slider->display_on_home = !$slider->display_on_home;
        $slider->save();

        return response()->json([
            'success' => true,
            'message' => 'Status slider berhasil diperbarui.'
        ]);
    }

    public function showHomeSlider()
    {
        $sliders = Slider::where('display_on_home', true)->latest()->get();
        return view('slider', compact('sliders'));
    }
}
