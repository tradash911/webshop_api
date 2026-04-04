<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,"logout"])->middleware('auth:sanctum'); 
// 🔹 verify link (amikor rákattint az emailben)
/* Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json([
        'message' => 'Email verified successfully'
    ]);
})->middleware(['signed'])->name('verification.verify'); */

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    // Ellenőrizd a hash-t
    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return response()->json(['message' => 'Invalid verification link'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified']);
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return response()->json(['message' => 'Email verified successfully']);
})->name('verification.verify');


// 🔹 újraküldés
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json([
        'message' => 'Verification link sent'
    ]);
})->middleware(['auth:sanctum', 'throttle:6,1']);
///Admin
Route::post('/admin',[AdminController::class,'adminLogin']);
///Products

Route::apiResource('products', ProductController::class)->except(['store,update,destroy']);

Route::middleware('auth:sanctum')->post('/products', [ProductController::class, 'store']);
Route::middleware('auth:sanctum')->put('/products/{product}', [ProductController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/products/{product}', [ProductController::class, 'destroy']);
 //Categories
 Route::apiResource('categories', CategoryController::class);
Route::middleware('auth:sanctum')->post('/orders', [OrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);