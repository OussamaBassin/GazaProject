<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;

// Protected routes 
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('/logout', [UserController::class, 'logout']);
    
    // Comments
    Route::post('/articles/{article}/comments', [CommentController::class, 'store']);
    Route::put('/articles/{article}/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/articles/{article}/comments/{comment}', [CommentController::class, 'destroy']);
    
    // User interactions
    Route::post('/articles/{article}/like', [ArticleController::class, 'toggleLike']);
    Route::post('/articles/{article}/favorite', [ArticleController::class, 'toggleFavorite']);

    Route::get('/users/{user}/favorites', [UserController::class, 'getFavoriteArticlesById']);
});

// Public routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// Public article routes
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/search', [ArticleController::class, 'search']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);
Route::get('/articles/{article}/comments', [CommentController::class, 'getCommentsByArticle']);

// User public info
Route::get('/articles/{article}/favorites-count', [ArticleController::class, 'getFavoriteCountForArticle']);






