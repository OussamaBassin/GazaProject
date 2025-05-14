<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Article::with(['comments'])
            ->latest('publishedAt')
            ->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'is_draft' => 'boolean'
        ]);

        $article = Auth::user()->articles()->create($validated);
        return response()->json($article, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $article->increment('views');
        return $article->load(['comments.user']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);
        $validated = $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
            'category' => 'string',
            'is_draft' => 'boolean'
        ]);

        $article->update($validated);
        return response()->json($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        $article->delete();
        return response()->json(null, 204);
    }

    /**
     * Get comments for the specified article.
     */
    public function getComments(Article $article)
    {
        // Fetch comments for the given article with the related user
        $comments = $article->comments()->with('user')->get();
        return response()->json($comments);
    }

    public function toggleLike(Article $article)
    {
        $user = Auth::user();
        if ($article->likes()->where('user_id', $user->id)->exists()) {
            $article->likes()->where('user_id', $user->id)->delete();
            return response()->json(['liked' => false]);
        }
        
        $article->likes()->create(['user_id' => $user->id]);
        return response()->json(['liked' => true]);
    }

    public function toggleFavorite(Article $article)
    {
        $user = Auth::user();
        if ($user->favorites()->where('article_id', $article->id)->exists()) {
            $user->favorites()->detach($article->id);
            return response()->json(['favorited' => false]);
        }
        
        $user->favorites()->attach($article->id);
        return response()->json(['favorited' => true]);
    }

    public function getUserDrafts()
    {
        return Auth::user()->articles()
            ->where('is_draft', true)
            ->latest()
            ->get();
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        return Article::where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('content', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->with(['comments'])
        ->latest('publishedAt')
        ->paginate(10);
    }



    public function getFavoriteCountForArticle(Article $article)
    {
        return response()->json([
            'favorites_count' => $article->favorites()->count()
        ]);
    }
}
