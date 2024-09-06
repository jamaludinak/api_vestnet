<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MobileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/registerMobile', [AuthController::class, 'registerMobile']);
Route::post('/verifyOTP', [AuthController::class, 'verifyOTP']);
Route::post('/loginMobile', [AuthController::class, 'loginMobile']);
Route::post('/resend-otp-email', [AuthController::class, 'resendOTPEmail']);

Route::post('/send_otp_whatsapp', [MobileController::class, 'sendOtp']);
Route::post('/validate_otp_whatsapp', [MobileController::class, 'validateOtp']);
Route::post('/resend-otp-wa', [AuthController::class, 'resendOtpWA']);

Route::get('/proyek/{id}', [MobileController::class, 'getProyekDetail']);
Route::get('proyek-aktif', [MobileController::class, 'getActiveProjects']);
Route::get('/detailProyekAll', [MobileController::class, 'getAllProyekDetails']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/getHomeData', [MobileController::class, 'getHomeData']);
    Route::post('/investInProject', [MobileController::class, 'investInProject']);
    Route::post('/submitPengajuanInvestasi', [MobileController::class, 'submitPengajuanInvestasi']);
    Route::post('/submitPengajuanInternet', [MobileController::class, 'submitPengajuanInternet']);
    Route::get('/user/profile', [MobileController::class, 'getUserProfile']);
    Route::post('/user/update', [MobileController::class, 'updateUserProfile']);
    Route::get('/getInvestasiData', [MobileController::class, 'getInvestasiData']);
    Route::get('/riwayatMutasi', [MobileController::class, 'riwayatMutasi']);
    Route::get('/getInvestDataDetail', [MobileController::class, 'getInvestDataDetail']);
    Route::get('/getProjectInvestDetail/{projectId}', [MobileController::class, 'getProjectInvestDetail']);
    Route::get('/user-invested-projects', [MobileController::class, 'getUserInvestedProjects']);
});


