<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all comments with their related user and article
        $comments = Comment::with(['user', 'article'])->get();
        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'article_id' => 'required|exists:articles,id',
        ]);

        // Create a new comment
        $comment = Comment::create($validated);

        return response()->json($comment, 201); // Return the created comment with a 201 status
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        // Return the comment with its related user and article
        return response()->json($comment->load(['user', 'article']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        // Validate the request
        $validated = $request->validate([
            'content' => 'sometimes|required|string',
            'user_id' => 'sometimes|required|exists:users,id',
            'article_id' => 'sometimes|required|exists:articles,id',
        ]);

        // Update the comment
        $comment->update($validated);

        return response()->json($comment); // Return the updated comment
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        // Delete the comment
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    /**
     * Get comments by article ID.
     */
    public function getCommentsByArticle($articleId)
    {
        // Fetch comments for the given article ID with the related user
        $comments = Comment::where('article_id', $articleId)->with('user')->get();
        return response()->json($comments);
    }

    /**
     * Get comments by user ID.
     */
    public function getCommentsByUser($userId)
    {
        // Fetch comments for the given user ID with the related article
        $comments = Comment::where('user_id', $userId)->with('article')->get();
        return response()->json($comments);
    }
}
