<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        return view('category-artikel.index', [
            'categories' => ArticleCategory::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        ArticleCategory::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Kategori artikel berhasil ditambahkan');
    }

    public function update(Request $request, ArticleCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return back()->with('success', 'Kategori artikel berhasil diperbarui');
    }

    public function destroy(ArticleCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Kategori artikel berhasil dihapus');
    }
}
