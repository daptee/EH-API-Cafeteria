<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Order
Route::post('order', [OrderController::class, 'store']);
Route::post('order/change/status', [OrderController::class, 'order_change_status']);
Route::get('order/{id}', [OrderController::class, 'show']);
Route::get('order/number/{order_number}', [OrderController::class, 'order_get_by_number']);
Route::get('order/status/list', [OrderController::class, 'get_status_list']);

// Payment
Route::post('payment', [PaymentController::class, 'store']);

// Product
Route::post('product/images', [ProductController::class, 'store']);
Route::get('product/images/{product_id}', [ProductController::class, 'product_images']);
Route::post('product/images/delete/{image_id}', [ProductController::class, 'product_images_delete']);
Route::get('product/images_principal', [ProductController::class, 'product_images_principal']);

// Categories images
Route::post('category/image', [CategoryController::class, 'store']);
Route::get('category/image/{cod_category}', [CategoryController::class, 'category_images']);
Route::post('category/images/delete/{image_id}', [CategoryController::class, 'delete_category_image']);

// Clear cache
Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('optimize');

    return response()->json([
        "message" => "Cache cleared successfully"
    ]);
});