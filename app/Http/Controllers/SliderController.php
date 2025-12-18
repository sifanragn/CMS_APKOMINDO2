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

        // ===============================
        // VALIDATION
        // ===============================
        $validated = $request->validate([
            'title'           => 'nullable|string|max:255',
            'subtitle'        => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'youtube_id'      => 'nullable|string|max:255',
            'button_text'     => 'nullable|string|max:100',
            'url_link'        => 'nullable|url|max:255',
            'display_on_home' => 'nullable|boolean',
        ]);

        // ===============================
        // IMAGE UTAMA (SLIDER)
        // ===============================
        // ===============================
// HANDLE EXTRA IMAGE FILES
// ===============================
if ($request->hasFile('extra_images')) {

    foreach ($request->file('extra_images') as $key => $value) {

        /**
         * ===============================
         * TAMBAH FOTO BARU (new_xxx)
         * ===============================
         */
        if (str_starts_with($key, 'new_') && is_array($value)) {

            foreach ($value as $file) {
                $path = $file->store('sliders/extra', 'public');

                SliderImage::create([
                    'slider_id' => $slider->id,
                    'image'     => $path,
                    'title'     => $request->extra_titles[$key] ?? null,
                    'subtitle'  => $request->extra_subtitles[$key] ?? null,
                ]);
            }
        }

        /**
         * ===============================
         * REPLACE FOTO EXISTING (1 FILE)
         * ===============================
         */
        if (is_numeric($key) && $value instanceof \Illuminate\Http\UploadedFile) {

            $img = SliderImage::find($key);
            if (!$img) continue;

            // hapus file lama
            if ($img->image && Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }

            // simpan file baru
            $img->image = $value->store('sliders/extra', 'public');

            // update text
            $img->title    = $request->extra_titles[$key] ?? $img->title;
            $img->subtitle = $request->extra_subtitles[$key] ?? $img->subtitle;

            $img->save();
        }
    }
}



        DB::commit();

        // ===============================
        // RESPONSE
        // ===============================
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Slider berhasil diperbarui.'
            ]);
        }

        return redirect()->route('slider.index')
            ->with('success', 'Slider berhasil diperbarui.');

    } catch (\Exception $e) {
        DB::rollBack();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', $e->getMessage());
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
