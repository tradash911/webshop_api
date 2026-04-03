<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,"logout"])->middleware('auth:sanctum'); 
///Products
/* Route::apiResource('products', ProductController::class);
Route::middleware('auth:sanctum')->post('/products', [ProductController::class, 'store']); */
Route::apiResource('products', ProductController::class)->except(['store']);
///Admin
Route::post('/admin',[AdminController::class,'adminLogin']);



Route::middleware('auth:sanctum')->post('/products', [ProductController::class, 'store']);
 //Categories
 Route::apiResource('categories', CategoryController::class);
Route::middleware('auth:sanctum')->post('/orders', [OrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('/orders', [OrderController::class, 'index']);