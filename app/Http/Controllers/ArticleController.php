<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        return view('artikel.index', [
            'articles'   => Article::with('category')->latest()->get(),
            'categories' => ArticleCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'content'     => 'required',
            'image'       => 'required|image|max:2048',
        ]);

        $data['slug']    = Str::slug($data['title']);
        $data['display'] = $request->has('display') ? 1 : 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->route('artikel.index')
    ->with('success', 'Artikel berhasil diperbarui');

    }

    public function update(Request $request, Article $artikel)
{
    $data = $request->validate([
        'title'       => 'required|string|max:255',
        'category_id' => 'nullable|exists:article_categories,id',
        'content'     => 'required',
        'image'       => 'nullable|image|max:2048',
    ]);

    $data['slug']    = Str::slug($data['title']);
    $data['display'] = $request->has('display');

    if ($request->hasFile('image')) {
        if ($artikel->image) {
            Storage::disk('public')->delete($artikel->image);
        }
        $data['image'] = $request->file('image')->store('articles', 'public');
    }

    $artikel->update($data);

    return back()->with('success', 'Artikel berhasil diperbarui');
}


    public function destroy(Article $article)
    {
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return back()->with('success', 'Artikel berhasil dihapus');
    }
}
