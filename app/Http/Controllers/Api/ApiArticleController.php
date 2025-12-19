<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiArticleController extends Controller
{
    /**
     * GET /api/articles
     * List artikel (Frontend)
     */
    public function index(Request $request)
    {
        $query = Article::with('category')
            ->where('display', 1)
            ->latest();

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $articles = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    /**
     * GET /api/articles/{slug}
     * Detail artikel
     */
    public function show($slug)
    {
        $article = Article::with('category')
            ->where('slug', $slug)
            ->where('display', 1)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }

    /**
     * POST /api/articles
     * Create artikel (CMS)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'content'     => 'required',
            'image'       => 'nullable|image|max:2048',
            'display'     => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['display'] = $request->display ?? 1;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil dibuat',
            'data' => $article
        ], 201);
    }

    /**
     * PUT /api/articles/{id}
     * Update artikel
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'content'     => 'required',
            'image'       => 'nullable|image|max:2048',
            'display'     => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['display'] = $request->display ?? $article->display;

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil diperbarui',
            'data' => $article
        ]);
    }

    /**
     * DELETE /api/articles/{id}
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil dihapus'
        ]);
    }
}
