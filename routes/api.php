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

Route::post('order', [OrderController::class, 'store']);
Route::post('order/change/status', [OrderController::class, 'order_change_status']);
Route::post('payment', [PaymentController::class, 'store']);
Route::get('order/{id}', [OrderController::class, 'show']);
// cafeteria y sukha (pago efectivo cambie estado)
Route::post('product/images', [ProductController::class, 'store']);
Route::get('product/images/{product_id}', [ProductController::class, 'product_images']);
Route::get('product/images_principal', [ProductController::class, 'product_images_principal']);

// Categories images
Route::post('category/image', [CategoryController::class, 'store']);
Route::get('category/image/{cod_category}', [CategoryController::class, 'category_images']);

// Clear cache
Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('optimize');

    return response()->json([
        "message" => "Cache cleared successfully"
    ]);
});