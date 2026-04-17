<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,"logout"])->middleware('auth:sanctum'); 

Route::get('/test-mail', function () {
    $resend = Resend::client(env('RESEND_API_KEY'));

    $resend->emails->send([
        'from' => 'onboarding@resend.dev',
        'to' => ['tradash@gmail.com'],
        'subject' => 'Hello from Laravel',
        'html' => '<p>It works!</p>',
    ]);

    return 'ok';
});

///Admin
Route::post('/admin',[AdminController::class,'adminLogin']);
Route::middleware('auth:sanctum')->get('/admin/users', [UserController::class, 'viewUsers']);
Route::middleware('auth:sanctum')->get('/admin/findUser', [UserController::class, 'findUser']);
Route::middleware('auth:sanctum')->get('/admin/viewOrders', [OrderController::class, 'viewOrders']);
Route::middleware('auth:sanctum')->get('/admin/findOrder', [OrderController::class, 'findOrder']);
//User Profile
Route::middleware('auth:sanctum')->get('/profile', [UserController::class, 'show']);
Route::middleware('auth:sanctum')->put('/profile', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/profile/{user}', [UserController::class, 'destroy']);
Route::middleware('auth:sanctum')->post('/profile/change-email', [AuthController::class, 'requestChangeEmailAddress']);
Route::get('/confirm-change-email', [AuthController::class, 'confirmEmailChange']);

//Reset user password
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
// 🔹 verify link (amikor rákattint az emailben)

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

///Products

//Route::apiResource('products', ProductController::class)->except(['store,update,destroy']);
Route::middleware('auth:sanctum')->post('/admin/products', [ProductController::class, 'store']);
Route::middleware('auth:sanctum')->put('/admin/products/{product}', [ProductController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/admin/products/{product}', [ProductController::class, 'destroy']);
 //Categories
 Route::middleware('auth:sanctum')->post('/categories', [CategoryController::class, 'store']);
 Route::middleware('auth:sanctum')->put('/categories/{category}', [CategoryController::class, 'update']);
 Route::middleware('auth:sanctum')->delete('/categories/{category}', [CategoryController::class, 'destroy']);
 Route::get('/categories/viewCategories', [CategoryController::class, 'viewCategories']);

 //Orders
Route::middleware('auth:sanctum')->post('/createOrder', [OrderController::class, 'store']);
//Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'viewOrders']);