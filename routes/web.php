<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FacilityController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\RentalController;
use App\Http\Controllers\Web\RoomController;
use App\Http\Controllers\Web\SettingController;
use App\Http\Controllers\Web\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kamar
    Route::resource('rooms', RoomController::class);

    Route::resource('facilities', FacilityController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/rooms/photos/{photo}/primary', [RoomController::class, 'setPrimaryPhoto'])->name('rooms.photos.primary');
    Route::delete('/rooms/photos/{photo}', [RoomController::class, 'deletePhoto'])->name('rooms.photos.delete');

    // Penyewa
    Route::resource('tenants', TenantController::class);
    Route::patch('/tenants/{tenant}/deactivate', [TenantController::class, 'deactivate'])->name('tenants.deactivate');

    // Kontrak
    Route::resource('rentals', RentalController::class);
    Route::patch('/rentals/{rental}/terminate', [RentalController::class, 'terminate'])->name('rentals.terminate');

    // Invoice
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::post('/invoices/generate', [InvoiceController::class, 'generateMonthly'])->name('invoices.generate');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');

    // Pembayaran
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);

    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/settings', [NotificationController::class, 'updateSettings'])->name('notifications.settings');
    Route::post('/notifications/send-manual', [NotificationController::class, 'sendManual'])->name('notifications.send-manual');
    Route::post('/notifications/test', [NotificationController::class, 'testConnection'])->name('notifications.test');
    Route::get('/notifications/tenant-data/{tenant}', [NotificationController::class, 'getTenantData'])->name('notifications.tenant-data');

    // Laporan
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
        Route::get('/payments/pdf', [ReportController::class, 'exportPaymentsPdf'])->name('payments.pdf');
        Route::get('/rooms', [ReportController::class, 'rooms'])->name('rooms');
    });

    // Pengaturan
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/settings/profile', [SettingController::class, 'profile'])->name('settings.profile');
    Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
});
