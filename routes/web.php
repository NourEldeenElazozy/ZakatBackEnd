<?php

use App\Http\Controllers\API\DonationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZakatNisabController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\RechargeCardController;
Route::get('/privace', function () {
    return view('privacy-policy');
});
Route::get('recharge-cards/export', [RechargeCardController::class, 'export'])->name('recharge-cards.export');

Route::resource('recharge-cards', RechargeCardController::class);
Route::post('/do-payment', [PaymentController::class, 'doPayment'])->name('test');
Route::get('/images', [ImageController::class, 'index'])->name('images.index');
Route::patch('/donations/{id}/update-status', [DonationController::class, 'updateStatus'])->name('donations.updateStatus');
// حذف صورة
Route::delete('/images/{id}', [ImageController::class, 'delete'])->name('image.delete');

// رفع صورة
Route::post('/upload-image', [ImageController::class, 'uploadImage'])->name('image.upload');
Route::get('/run-migrations', function () {
    try {
        // استدعاء الترحيل باستخدام أمر Artisan
        Artisan::call('migrate');
        return "تم تشغيل الترحيل بنجاح";
    } catch (Exception $e) {
        // التعامل مع أي خطأ محتمل
        return "حدث خطأ أثناء تشغيل الترحيل: " . $e->getMessage();
    }
});
Route::get('/', function () {
    return view('auth.login');


});

Auth::routes(['register' => false]);

Route::resource('zakat_nisab', ZakatNisabController::class);
Route::resource( 'categories' ,CategoriesController::class);

Route::resource( 'campaign' ,CampaignController::class);

Route::resource( 'donation' ,DonationsController::class);
Route::patch('/donations/{id}/mark-as-pending', [DonationsController::class, 'markAsPending'])->name('donations.markAsPending');
Route::get('donations/{id}',[ CampaignController::class ,'index2']);


Route::prefix('reports')->group(function () {
    Route::get('/summary', [ReportController::class, 'generalSummary'])->name('reports.summary');
    Route::get('/by-category', [ReportController::class, 'donationsByCategory'])->name('reports.categories');
    Route::get('/by-type', [ReportController::class, 'donationsByType'])->name('reports.types');
    Route::get('/detailed', [ReportController::class, 'dateRangeReport'])->name('reports.detailed');
});

Route::middleware('auth')->group(function () {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

});



Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
Route::post('/notifications/send', [NotificationController::class, 'sendNotification'])->name('notifications.send');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/{page}', 'App\Http\Controllers\AdminController@index');


