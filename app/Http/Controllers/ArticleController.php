<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with('category')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        abort_if($article->status !== 'published' || $article->published_at === null || $article->published_at->isFuture(), 404);

        return view('articles.show', compact('article'));
    }
}
