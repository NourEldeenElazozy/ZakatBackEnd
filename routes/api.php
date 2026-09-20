<?php

use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\NisabController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DonationController;
use App\Http\Controllers\API\Chat\CustomerChatController;
use App\Http\Controllers\API\Chat\AdminChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\API\OnlinePaymentController;
use App\Http\Controllers\API\NotificationControllerApi;
use App\Http\Controllers\API\AchievementController;


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
Route::get('achievements/all', [AchievementController::class, 'index']);
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

Route::post('/donations/upload-receipt', [DonationController::class, 'uploadReceipt']);
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
Route::get('active_campaigns', [CampaignController::class, 'activeCampaigns']);
Route::get('active-campaigns', [CampaignController::class, 'activeCampaigns']);
Route::get('ezone/payment-callback', [OnlinePaymentController::class, 'paymentCallback']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Chat Routes — Customer
|--------------------------------------------------------------------------
*/
Route::prefix('chat')->group(function () {
    // Create or fetch an existing open conversation
    Route::post('conversations', [CustomerChatController::class, 'createConversation']);

    // List conversations for a user
    Route::get('conversations', [CustomerChatController::class, 'listConversations']);

    // Show single conversation
    Route::get('conversations/{conversation}', [CustomerChatController::class, 'showConversation']);

    // Messages
    Route::get('conversations/{conversation}/messages', [CustomerChatController::class, 'getMessages']);
    Route::post('conversations/{conversation}/messages', [CustomerChatController::class, 'sendMessage']);

    // Read & Close
    Route::post('conversations/{conversation}/read', [CustomerChatController::class, 'markAsRead']);
    Route::post('conversations/{conversation}/close', [CustomerChatController::class, 'closeConversation']);
});

/*
|--------------------------------------------------------------------------
| Chat Routes — Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin/chat')->group(function () {
    // List all conversations
    Route::get('conversations', [AdminChatController::class, 'listConversations']);

    // Show single conversation
    Route::get('conversations/{conversation}', [AdminChatController::class, 'showConversation']);

    // Messages
    Route::get('conversations/{conversation}/messages', [AdminChatController::class, 'getMessages']);
    Route::post('conversations/{conversation}/messages', [AdminChatController::class, 'sendMessage']);

    // Read & Close
    Route::post('conversations/{conversation}/read', [AdminChatController::class, 'markAsRead']);
    Route::post('conversations/{conversation}/close', [AdminChatController::class, 'closeConversation']);
});

Route::get('/bank-accounts', [App\Http\Controllers\API\BankAccountController::class, 'index']);
