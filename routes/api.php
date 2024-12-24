<?php

use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\NisabController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

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
Route::post('/donate', [DonationController::class, 'store']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/images/all', [ImageController::class, 'fetchAllImages'])->name('images.all');
Route::resource('/categories', CategoryController::class); // الحصول على جميع الفئات
Route::resource('/campaigns', CampaignController::class); // الحصول على جميع الفئات
Route::resource('/nisab', NisabController::class); // الحصول على جميع

Route::post('register', [AuthController::class, 'register']);
Route::get('soon', [CampaignController::class, 'soon']);
Route::get('open_campaign', [CampaignController::class, 'open_campaign']);

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);

