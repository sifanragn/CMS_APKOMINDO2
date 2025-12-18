<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ourblog;
use App\Models\BlogImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OurblogController extends Controller
{

    public function index()
    {
        $ourblogs = OurBlog::with(['category', 'extraImages'])->latest()->get();
        $categories = Category::all();
        return view('ourblogs.index', compact('ourblogs', 'categories'));
    }

    public function show($id)
{
    $blog = Ourblog::with(['category', 'extraImages'])->findOrFail($id);
    return view('ourblogs.show', compact('blog'));
}


    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'pub_date' => 'required|date',
        'category_id' => 'required|exists:categories,id',
        'waktu_baca' => 'required|string|max:75'
    ]);

    // SIMPAN IMAGE UTAMA
    $imagePath = $request->file('image')->store('ourblogs', 'public');

    $blog = Ourblog::create([
        'title' => $request->title,
        'description' => $request->description,
        'image' => $imagePath,
        'pub_date' => $request->pub_date,
        'category_id' => $request->category_id,
        'waktu_baca' => $request->waktu_baca,
    ]);

    /* ===============================
       SIMPAN FOTO TAMBAHAN
    =============================== */
    if ($request->hasFile('extra_images')) {
        foreach ($request->extra_images as $key => $file) {
            if (!$file) continue;

            $path = $file->store('ourblogs/extra', 'public');

            BlogImage::create([
                'ourblog_id' => $blog->id,
                'image' => $path,
                'title' => $request->extra_titles[$key] ?? null,
                'subtitle' => $request->extra_subtitles[$key] ?? null,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Data berhasil ditambahkan');
}

    public function update(Request $request, $id)
{
    $blog = Ourblog::findOrFail($id);

    $request->validate([
        'title'        => 'required|string|max:255',
        'description'  => 'required|string',
        'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'pub_date'     => 'required|date',
        'category_id'  => 'required|exists:categories,id',
        'waktu_baca'   => 'required|string|max:75'
    ]);

    /* ===============================
       UPDATE DATA UTAMA BLOG
    =============================== */
    $blog->title        = $request->title;
    $blog->description  = $request->description;
    $blog->pub_date     = $request->pub_date;
    $blog->category_id  = $request->category_id;
    $blog->waktu_baca   = $request->waktu_baca;

    if ($request->hasFile('image')) {
        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->image = $request->file('image')->store('ourblogs', 'public');
    }

    $blog->save();

    /* ===============================
       🔁 UPDATE TITLE & SUBTITLE FOTO LAMA
       (TANPA HARUS UPLOAD IMAGE)
    =============================== */
    if ($request->has('extra_titles') || $request->has('extra_subtitles')) {

        foreach ($request->extra_titles ?? [] as $key => $title) {

            // hanya proses ID lama
            if (!is_numeric($key)) continue;

            $extra = BlogImage::find($key);
            if (!$extra) continue;

            $extra->title    = $title;
            $extra->subtitle = $request->extra_subtitles[$key] ?? $extra->subtitle;
            $extra->save();
        }
    }
    if ($request->filled('delete_extra_ids')) {
    foreach ($request->delete_extra_ids as $id) {
        $extra = BlogImage::find($id);
        if (!$extra) continue;

        if ($extra->image && Storage::disk('public')->exists($extra->image)) {
            Storage::disk('public')->delete($extra->image);
        }

        $extra->delete();
    }
}

    /* ===============================
       🖼️ HANDLE IMAGE TAMBAHAN
       (UPLOAD BARU / GANTI IMAGE)
    =============================== */
    if ($request->hasFile('extra_images')) {

        foreach ($request->file('extra_images') as $key => $file) {

            if (!$file instanceof \Illuminate\Http\UploadedFile) continue;

            /**
             * ➕ TAMBAH FOTO BARU
             */
            if (str_starts_with($key, 'new')) {

                $path = $file->store('ourblogs/extra', 'public');

                BlogImage::create([
                    'ourblog_id' => $blog->id,
                    'image'      => $path,
                    'title'      => $request->extra_titles[$key] ?? null,
                    'subtitle'   => $request->extra_subtitles[$key] ?? null,
                ]);
            }

            /**
             * 🔁 GANTI IMAGE FOTO LAMA
             */
            if (is_numeric($key)) {

                $extra = BlogImage::find($key);
                if (!$extra) continue;

                if ($extra->image && Storage::disk('public')->exists($extra->image)) {
                    Storage::disk('public')->delete($extra->image);
                }

                $extra->image = $file->store('ourblogs/extra', 'public');
                $extra->save();
            }
        }
    }

    return redirect()->back()->with('success', 'Data berhasil diperbarui');
}


    public function destroy($id)
    {
        $blog = Ourblog::findOrFail($id);
        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ids = $request->input('ids');

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada Berita yang dipilih');
        }

        try {
            $products = Ourblog::whereIn('id', $ids)->get();

            foreach ($products as $product) {
                // Hapus gambar dari storage jika ada
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                // Hapus produk
                $product->delete();
            }

            return back()->with('success', 'Produk terpilih berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
