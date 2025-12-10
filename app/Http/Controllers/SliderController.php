<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->get();
        return view('slider.index', compact('sliders'));
    }

    /**
     * Simpan slider baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'          => 'nullable|string|max:255',
                'subtitle'       => 'nullable|string',
                'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'youtube_id'     => 'nullable|string|max:255',
                'button_text'    => 'nullable|string|max:100',
                'url_link'       => 'nullable|url|max:255',
                'display_on_home' => 'nullable|boolean',
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('sliders', 'public');
            }

            // Handle checkbox - pastikan boolean value
            $validated['display_on_home'] = $request->has('display_on_home') ? 1 : 0;

            // Create slider
            $slider = Slider::create($validated);

            if ($slider) {
                // Check if request is AJAX
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Slider berhasil ditambahkan.'
                    ]);
                }

                return redirect()->route('slider.index')->with('success', 'Slider berhasil ditambahkan.');
            } else {
                // Check if request is AJAX
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menambahkan slider.'
                    ], 500);
                }

                return redirect()->back()->with('error', 'Gagal menambahkan slider.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check if request is AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa data yang dimasukkan.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa data yang dimasukkan.');
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update slider
     */
    public function update(Request $request, $id)
    {
        try {
            $slider = Slider::findOrFail($id);

            $validated = $request->validate([
                'title'          => 'nullable|string|max:255',
                'subtitle'       => 'nullable|string',
                'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'youtube_id'     => 'nullable|string|max:255',
                'button_text'    => 'nullable|string|max:100',
                'url_link'       => 'nullable|url|max:255',
                'display_on_home' => 'nullable|boolean',
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                    Storage::disk('public')->delete($slider->image);
                }
                $validated['image'] = $request->file('image')->store('sliders', 'public');
            }

            // Handle checkbox
            $validated['display_on_home'] = $request->has('display_on_home') ? 1 : 0;

            $slider->update($validated);

            // Check if request is AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slider berhasil diperbarui.'
                ]);
            }

            return redirect()->route('slider.index')->with('success', 'Slider berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check if request is AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa data yang dimasukkan.',
                    'errors' => $e->validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa data yang dimasukkan.');
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus slider
     */
    public function destroy(Request $request, $id)
    {
        try {
            $slider = Slider::findOrFail($id);

            // Hapus file gambar jika ada
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $slider->delete();

            // Check if request is AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slider berhasil dihapus.'
                ]);
            }

            return redirect()->route('slider.index')->with('success', 'Slider berhasil dihapus.');
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
