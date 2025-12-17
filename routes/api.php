<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public Routes (មិនចាំបាច់ Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (ត្រូវមាន Token)
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/update', [AuthController::class, 'update']); // ប្រើ POST សម្រាប់ update ដូចក្នុង Postman របស់អ្នក
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::delete('/delete', [AuthController::class, 'destroy']);
});