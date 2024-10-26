<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/verify', [VerificationController::class, 'verify']);

// Protected routes for authenticated users only
Route::middleware('auth:sanctum')->group(function () {
    // Retrieve authenticated user details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Tags Resource
    Route::get('/tags', [TagController::class, 'index']); // View all tags
    Route::post('/tags', [TagController::class, 'store']); // Create a new tag
    Route::put('/tags/{id}', [TagController::class, 'update']); // Update a specific tag
    Route::delete('/tags/{id}', [TagController::class, 'destroy']); // Delete a specific tag

    // Posts Resource
    Route::get('/posts', [PostController::class, 'index']); // View user's posts
    Route::post('/posts', [PostController::class, 'store']); // Create a new post
    Route::get('/posts/{id}', [PostController::class, 'show']); // View a single post
    Route::put('/posts/{id}', [PostController::class, 'update']); // Update a specific post
    Route::delete('/posts/{id}', [PostController::class, 'destroy']); // Soft delete a specific post
    Route::get('deleted/posts',[PostController::class,'viewDeleted']); // View all deleted posts
    Route::patch('/posts/{id}/restore', [PostController::class, 'restore']); // Restore a deleted post

    // Stats Endpoint
    Route::get('/stats', [StatsController::class, 'index']); // Get application statistics
});
