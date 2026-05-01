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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Http;

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,"logout"])->middleware('auth:sanctum'); 
Route::get('/test-mail', function () {

    $response = Http::withHeaders([
        'accept' => 'application/json',
        'content-type' => 'application/json',
        'api-key' => env('BREVO_API_KEY'),
    ])->post('https://api.brevo.com/v3/smtp/email', [
        'sender' => [
            'name' => 'My App',
            'email' => 'tradash@gmail.com',
        ],
        'to' => [
            [
                'email' => 'tradash666@gmail.com',
                'name' => 'User',
            ]
        ],
        'subject' => 'Hello from Brevo API',
        'htmlContent' => '<h1>Szia!</h1><p>Működik API-val 🚀</p>',
    ]);

    return $response->body();
});
Route::get('/brevo-test', function () {
    return [
        'key' => env('BREVO_API_KEY'),
    ];
});



Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');

    return 'cleared';
});

///Admin
Route::post('/admin',[AdminController::class,'adminLogin']);
Route::middleware('auth:sanctum')->get('/admin/users', [UserController::class, 'viewUsers']);
Route::middleware('auth:sanctum')->get('/admin/findUser', [UserController::class, 'findUser']);
Route::middleware('auth:sanctum')->get('/admin/viewOrders', [OrderController::class, 'viewOrders']);
Route::middleware('auth:sanctum')->get('/admin/findOrder', [OrderController::class, 'findOrder']);

Route::middleware('auth:sanctum')->post('/admin/products', [ProductController::class, 'store']);
Route::middleware('auth:sanctum')->put('/admin/products/{product}', [ProductController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/admin/products/{product}', [ProductController::class, 'destroy']);
//User Profile
Route::middleware('auth:sanctum')->get('/profile', [UserController::class, 'show']);
Route::middleware('auth:sanctum')->put('/profile', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/profile/{user}', [UserController::class, 'destroy']);
//Change email address
Route::middleware('auth:sanctum')->post('/profile/change-email', [AuthController::class, 'requestChangeEmailAddress']);
Route::get('/email/change/confirm/{token}', [AuthController::class,'confirmEmailChange'])->name('email.change.confirm');


// Registration verify link 

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    // signature check
    if (! $request->hasValidSignature()) {
        return response()->json(['message' => 'Invalid or expired link'], 403);
    }

    $user = User::findOrFail($id);

    // hash check
    if (! hash_equals($hash, sha1($user->email))) {
        return response()->json(['message' => 'Invalid hash'], 403);
    }

    // már verified?
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Already verified']);
    }

    // verify
    $user->markEmailAsVerified();

    return response()->json([
        'message' => 'Email verified successfully'
    ]);

})->middleware(['signed'])->name('verification.verify');


// email újra  küldése
Route::post('/email/resend', function (Request $request) {
    $user = $request->user();

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Already verified']);
    }

    app(App\Services\VerificationMailService::class)->send($user);

    return response()->json(['message' => 'Verification email resent']);
})->middleware('auth:sanctum');

//Reset user password
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);


///Products
Route::get('/products',[CategoryController::class,'viewCategories']);
//Route::apiResource('products', ProductController::class)->except(['store,update,destroy']);

 //Categories
 Route::middleware('auth:sanctum')->post('/categories', [CategoryController::class, 'store']);
 Route::middleware('auth:sanctum')->put('/categories/{category}', [CategoryController::class, 'update']);
 Route::middleware('auth:sanctum')->delete('/categories/{category}', [CategoryController::class, 'destroy']);
 //Route::get('/categories/viewCategories', [CategoryController::class, 'viewCategories']);

 //Orders
//Route::middleware('auth:sanctum')->post('/createOrder', [OrderController::class, 'store'])->middleware('throttle:10,1');
Route::post('/createOrder',[OrderController::class,'store']);
//Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'viewOrders']);