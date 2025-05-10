<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;

// Protected routes 
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('articles', ArticleController::class);
    Route::apiResource('comments', CommentController::class);
    Route::post('/logout', [UserController::class, 'logout']);
});

// Public routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);
Route::get('/articles/{article}/comments', [CommentController::class, 'getCommentsByArticle']);
Route::get('/users/{users}/comments', [CommentController::class, 'getCommentsByUser']);




