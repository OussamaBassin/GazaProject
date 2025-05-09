<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all articles with their related comments and author
        $articles = Article::with(['comments'])->get();
        return response()->json($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'image' => 'nullable|string',
            'link' => 'nullable|string',
        ]);

        // Create a new article
        $article = Article::create($validated);

        return response()->json($article, 201); // Return the created article with a 201 status
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        // Return the article with its related comments and author
        return response()->json($article->load(['comments']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'author' => 'sometimes|required|string|max:255',
            'image' => 'nullable|string',
            'link' => 'nullable|string',
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        // Update the article
        $article->update($validated);

        return response()->json($article); // Return the updated article
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Delete the article
        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
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
}
