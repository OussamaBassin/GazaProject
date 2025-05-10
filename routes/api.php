<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
<<<<<<< HEAD
=======

// Public routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);
Route::get('/articles/{article}/comments', [CommentController::class, 'getCommentsByArticle']);
Route::get('/users/{users}/comments', [CommentController::class, 'getCommentsByUser']);

Route::get('/users/{user}/favorites', [UserController::class, 'getFavoriteArticlesById']);
Route::get('/articles/{user}/favorites', [ArticleController::class, 'getFavoriteCountForArticle']);




>>>>>>> a411296 (nearly there)
