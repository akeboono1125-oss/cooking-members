<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $inCategory = $request->input('category');
        $inKeyword = $request->input('keyword');
        $articles = Article::with('category')
            ->published()
            ->when($inCategory, function ($query, $inCategory) {
                $query->where('category_id', $inCategory);
            })
            ->when($inKeyword, function ($query, $inKeyword) {
                $query->where(function ($q) use ($inKeyword) {
                    $q->where('title', 'like', "%{$inKeyword}%");
                    $q->orWhere('body', 'like', "%{$inKeyword}%");
                });
            })
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('id')->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    public function show(Request $request, Article $article): View
    {
        abort_unless($article->isPublished(), 404);
        $isFavorite = $request->user()
            ->favoriteArticles()
            ->where('articles.id', $article->id)
            ->exists();

        return view('articles.show', compact('article', 'isFavorite'));
    }
}
