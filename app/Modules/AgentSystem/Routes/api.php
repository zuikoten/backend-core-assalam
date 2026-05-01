<?php
// app/Modules/AgentSystem/Routes/api.php
// Module Routes - Agent System

use App\Modules\AgentSystem\Controllers\AuthController;
use App\Modules\AgentSystem\Controllers\AgentController;
use App\Modules\AgentSystem\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public - Authentication
Route::post('/login', [AuthController::class, 'login']);

// Public - Password Reset
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp',      [AuthController::class, 'verifyOtp']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// Public - Get Registration Fee
Route::get('/settings/registration-fee', function () {
    return response()->json([
        'cabang'  => (int) \App\Models\Setting::get('registration_fee_cabang', 0),
        'premium' => (int) \App\Models\Setting::get('registration_fee_premium', 0),
    ]);
});

// Public - Data Wilayah
Route::get('/regions/provinces', function () {
    return \App\Models\Region::where('region_type', 'province')
        ->orderBy('name')
        ->get(['id', 'name']);
});

Route::get('/regions/regencies/{provinceId}', function ($provinceId) {
    return \App\Models\Region::where('region_type', 'regency')
        ->where('parent_id', $provinceId)
        ->orderBy('name')
        ->get(['id', 'name']);
});

Route::get('/regions/districts/{regencyId}', function ($regencyId) {
    return \App\Models\Region::where('region_type', 'district')
        ->where('parent_id', $regencyId)
        ->orderBy('name')
        ->get(['id', 'name']);
});

// 📝 PUBLIC ROUTE - Self Registration (Step 1, no auth required)
Route::post('/agents/register', [AgentController::class, 'register']);

// 🛡️ PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/email/send-otp',  [AuthController::class, 'sendEmailVerification']);
    Route::post('/email/verify',    [AuthController::class, 'verifyEmail']);

    // 👤 AGENT ROUTES (Steps 2-5)
    Route::prefix('agents')->group(function () {
        Route::get('/{agent}/status', [AgentController::class, 'status']);
        Route::post('/{agent}/documents', [AgentController::class, 'uploadDocuments']);
        Route::put('/{agent}/address', [AgentController::class, 'updateAddress']);
        Route::put('/{agent}/bank', [AgentController::class, 'updateBank']);
        Route::post('/{agent}/payment', [AgentController::class, 'uploadPaymentProof']);
    });

    // 👑 ADMIN ROUTES - Double protection
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/pending-agents', [AdminController::class, 'pendingAgents']);
        Route::put('/documents/{document}/verify', [AdminController::class, 'verifyDocuments']);
        Route::put('/payments/{payment}/verify', [AdminController::class, 'verifyPayment']);
        Route::put('/agents/{agent}/status', [AdminController::class, 'updateAgentStatus']);
    });
});
