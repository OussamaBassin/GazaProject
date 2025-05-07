<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all users with their comments and favorite articles
        $users = User::with(['favorites'])->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Create a new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json($user, 201); // Return the created user with a 201 status
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch a specific user with their comments and favorite articles
        $user = User::with(['favorites'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        // Find the user and update their information
        $user = User::findOrFail($id);
        $user->update([
            'name' => $validated['name'] ?? $user->name,
            'email' => $validated['email'] ?? $user->email,
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : $user->password,
        ]);

        return response()->json($user); // Return the updated user
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the user and delete them
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Get liked articles for the specified user.
     */
    public function getLikedArticles(string $id)
    {
        // Fetch liked articles for the given user ID
        $likedArticles = User::findOrFail($id)->likedArticles()->get();
        return response()->json($likedArticles);
    }

    /**
     * Get comments for the specified user.
     */
    public function getComments(string $id)
    {
        // Fetch comments for the given user ID
        $comments = User::findOrFail($id)->comments()->with('article')->get();
        return response()->json($comments);
    }

    /**
     * Get favorite articles for the specified user.
     */
    public function getFavoriteArticles(string $id)
    {
        // Fetch favorite articles for the given user ID
        $favoriteArticles = User::findOrFail($id)->favoritedArticles()->get();
        return response()->json($favoriteArticles);
    }
}