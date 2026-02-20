<?php

use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\NisabController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\API\OnlinePaymentController;
use App\Http\Controllers\API\NotificationControllerApi;



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
Route::post('/check-phone', [AuthController::class, 'checkPhoneExists']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('mobiCash', [OnlinePaymentController::class, 'mobiCash']);
Route::post('verifymobiCash', [OnlinePaymentController::class, 'verifymobiCash']);
Route::post('/masarat', [OnlinePaymentController::class, 'signin']);
Route::post('/openSession', [OnlinePaymentController::class, 'openSession']);
Route::post('/completeSession', [OnlinePaymentController::class, 'completeSession']);
Route::get('/notifications', [NotificationControllerApi::class, 'getUserNotifications']);
Route::get('user-donations/{userId}', [DonationController::class, 'getUserDonations']);
Route::post('/do-payment', [PaymentController::class, 'doPayment']);
Route::post('/do-Conf', [PaymentController::class, 'doConf']);

Route::post('/donate', [DonationController::class, 'store']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/images/all', [ImageController::class, 'fetchAllImages'])->name('images.all');
Route::resource('/categories', CategoryController::class); // الحصول على جميع الفئات
Route::resource('/campaigns', CampaignController::class); // الحصول على جميع الفئات
Route::resource('/nisab', NisabController::class); // الحصول على جميع
Route::get('/fix-campaigns', [CampaignController::class, 'fixPreviousCampaigns']); 
Route::post('register', [AuthController::class, 'register']);
Route::post('ezone/create-link', [OnlinePaymentController::class, 'createEzoneLink']);
Route::get('completedCampaigns', [CampaignController::class, 'completedCampaigns']);
Route::get('soon', [CampaignController::class, 'soon']);
Route::get('open_campaign', [CampaignController::class, 'open_campaign']);
Route::get('ezone/payment-callback', [OnlinePaymentController::class, 'paymentCallback']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);

