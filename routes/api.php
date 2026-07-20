<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AuthController,
    ClientController,
    CompanyController,
    DashboardController,
    GSTInvoiceController,
    ProformaController,
    ProductController,
    ReportController,
    SettingController,
};

/*
|--------------------------------------------------------------------------
| API Routes - v1
|--------------------------------------------------------------------------
| Prefix: /api/v1
| Auth: Laravel Sanctum (Bearer Token)
| Format: JSON
| Error Format: RFC 7807 (Problem Details)
*/

// ============================================
// PUBLIC ROUTES (No Auth Required)
// ============================================
Route::prefix('v1')->group(function () {

    // Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('api.health');

    // Authentication
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.password.forgot');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.password.reset');
});

// ============================================
// PROTECTED ROUTES (Sanctum Token Required)
// ============================================
Route::prefix('v1')->middleware(['auth:sanctum', 'company.selected.api'])->group(function () {

    // ─────────────────────────────────────
    // Authenticated User
    // ─────────────────────────────────────
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    Route::put('/user/profile', [AuthController::class, 'updateProfile'])->name('api.user.profile');
    Route::put('/user/password', [AuthController::class, 'updatePassword'])->name('api.user.password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // ─────────────────────────────────────
    // Dashboard
    // ─────────────────────────────────────
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');

    // ─────────────────────────────────────
    // Company
    // ─────────────────────────────────────
    Route::prefix('company')->name('api.company.')->group(function () {
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('/current', [CompanyController::class, 'current'])->name('current');
        Route::get('/switch', [CompanyController::class, 'switchCompany'])->name('switch');
        Route::post('/switch/{company}', [CompanyController::class, 'setCurrent'])->name('set');
    });

    // ─────────────────────────────────────
    // Settings (Company Settings)
    // ─────────────────────────────────────
    Route::prefix('settings')->name('api.settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
        Route::post('/upload-logo', [SettingController::class, 'uploadLogo'])->name('upload.logo');
        Route::post('/upload-signature', [SettingController::class, 'uploadSignature'])->name('upload.signature');
        Route::delete('/media', [SettingController::class, 'removeMedia'])->name('remove.media');
    });

    // ─────────────────────────────────────
    // Clients
    // ─────────────────────────────────────

    Route::get('clients/search', [ClientController::class, 'search'])->name('api.clients.search');
    Route::get('clients/filter/state', [ClientController::class, 'filterByState'])->name('api.clients.filter.state');
    Route::get('clients/filter/status', [ClientController::class, 'filterByStatus'])->name('api.clients.filter.status');
    Route::apiResource('clients', ClientController::class)->names('api.clients');

    // ─────────────────────────────────────
    // Products
    // ─────────────────────────────────────

    Route::get('products/search', [ProductController::class, 'search'])->name('api.products.search');
    Route::apiResource('products', ProductController::class)->names('api.products');
    Route::get('products/{product}/stock-info', [ProductController::class, 'stockInfo'])
        ->name('api.products.stock-info');

    // ─────────────────────────────────────
    // Proforma Invoices
    // ─────────────────────────────────────
    Route::apiResource('proformas', ProformaController::class)->names('api.proformas');
    Route::post('proformas/{id}/convert-to-gst', [ProformaController::class, 'convertToGst'])
        ->name('api.proformas.convert-to-gst')
        ->where('id', '[0-9]+');
    Route::get('proformas/{id}/pdf', [ProformaController::class, 'pdf'])
        ->name('api.proformas.pdf')
        ->where('id', '[0-9]+');
    Route::get('proformas/{id}/preview', [ProformaController::class, 'stream'])
        ->name('api.proformas.preview')
        ->where('id', '[0-9]+');
    Route::post('proformas/{id}/send-email', [ProformaController::class, 'sendEmail'])
        ->name('api.proformas.send-email')
        ->where('id', '[0-9]+');

    // ─────────────────────────────────────
    // GST Invoices
    // ─────────────────────────────────────
    Route::apiResource('gst-invoices', GSTInvoiceController::class)->names('api.gst-invoices');
    Route::get('gst-invoices/{id}/pdf', [GSTInvoiceController::class, 'pdf'])
        ->name('api.gst-invoices.pdf')
        ->where('id', '[0-9]+');
    Route::get('gst-invoices/{id}/preview', [GSTInvoiceController::class, 'stream'])
        ->name('api.gst-invoices.preview')
        ->where('id', '[0-9]+');
    Route::post('gst-invoices/bulk-pdf', [GSTInvoiceController::class, 'bulkPdf'])
        ->name('api.gst-invoices.bulk-pdf');
    Route::post('gst-invoices/{id}/send-email', [GSTInvoiceController::class, 'sendEmail'])
        ->name('api.gst-invoices.send-email')
        ->where('id', '[0-9]+');

    // ─────────────────────────────────────
    // Reports
    // ─────────────────────────────────────
    Route::prefix('reports')->name('api.reports.')->group(function () {
        Route::get('/outstanding', [ReportController::class, 'outstanding'])->name('outstanding');
        Route::get('/gstr1', [ReportController::class, 'gstr1'])->name('gstr1');
        Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    });
});

// ============================================
// RATE-LIMITED WRITE OPERATIONS
// ============================================
Route::prefix('v1')->middleware(['auth:sanctum', 'company.selected.api', 'throttle:invoice-create'])->group(function () {
    Route::post('proformas', [ProformaController::class, 'store'])->name('api.proformas.store');
    Route::post('gst-invoices', [GSTInvoiceController::class, 'store'])->name('api.gst-invoices.store');
});

Route::prefix('v1')->middleware(['auth:sanctum', 'company.selected.api', 'throttle:invoice-delete'])->group(function () {
    Route::delete('proformas/{id}', [ProformaController::class, 'destroy'])
        ->name('api.proformas.destroy')
        ->where('id', '[0-9]+');
    Route::delete('gst-invoices/{id}', [GSTInvoiceController::class, 'destroy'])
        ->name('api.gst-invoices.destroy')
        ->where('id', '[0-9]+');
});

// Premium rate-limited routes (120/min for paid plans)
Route::prefix('v1')->middleware(['auth:sanctum', 'company.selected.api', 'throttle:api-premium'])->group(function () {
    // These routes get higher rate limits for premium users
    // Stubbed for future paid plan integration
});
