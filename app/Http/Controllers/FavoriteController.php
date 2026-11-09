<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()
            ->favoriteArticles()
            ->published()
            ->with('category')
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->isPublished(), 404);
        $request->user()
            ->favoriteArticles()
            ->syncWithoutDetaching([$article->id]);

        return back();
    }

    public function destroy(Request $request, Article $article): RedirectResponse
    {
        $request->user()
            ->favoriteArticles()
            ->detach([$article->id]);

        return back();
    }
}
