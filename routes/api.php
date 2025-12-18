<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public Routes (មិនចាំបាច់ Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);





// Protected Routes (តម្រូវឱ្យ Login ជាមុន)
Route::group(['middleware' => ['auth:sanctum']], function () {

    // User
    Route::post('/update', [AuthController::class, 'update']); // ប្រើ POST សម្រាប់ update ដូចក្នុង Postman របស់អ្នក
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::delete('/delete', [AuthController::class, 'destroy']);
    
    // --- Posts ---
    Route::get('/posts', [PostController::class, 'index']); // មើល Post ទាំងអស់
    Route::post('/posts', [PostController::class, 'store']); // បង្កើត Post
    Route::get('/posts/{id}', [PostController::class, 'show']); // មើល Post មួយ
    Route::post('/posts/{id}', [PostController::class, 'update']); // កែ Post (ប្រើ POST ជំនួស PUT ពេលមាន upload រូប)
    Route::delete('/posts/{id}', [PostController::class, 'destroy']); // លុប Post

    // --- Comments ---
    // បង្កើត Comment លើ Post ណាមួយ
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']); 
    // កែ Comment
    Route::put('/comments/{id}', [CommentController::class, 'update']); 
    // លុប Comment
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']); 

    // --- Likes ---
    // Like ឬ Unlike Post
    Route::post('/posts/{id}/likes', [LikeController::class, 'likeOrUnlike']); 
});