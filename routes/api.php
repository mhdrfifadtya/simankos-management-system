<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');

    // Protected routes
    Route::middleware(['auth:sanctum', 'role:penghuni'])->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/auth/profile', [AuthController::class, 'profile'])->name('api.profile');

        // Tagihan
        Route::get('/invoices/summary', [InvoiceController::class, 'summary'])->name('api.invoices.summary');
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('api.invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('api.invoices.show');

        // Pembayaran
        Route::get('/payments', [PaymentController::class, 'index'])->name('api.payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('api.payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('api.payments.show');

        // Kamar
        Route::get('/room', [RoomController::class, 'show'])->name('api.room');

        // Notifikasi
        Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
        Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('api.notifications.read-all');
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.read');

    });
});
