<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProformaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GSTInvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;


// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['company.selected'])
        ->name('dashboard');

    // Company Management
    Route::prefix('company')->name('company.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/settings', [CompanyController::class, 'settings'])->name('settings');
        Route::put('/settings', [CompanyController::class, 'updateSettings'])->name('settings.update');
        Route::put('/gst', [CompanyController::class, 'updateGst'])->name('gst.update');
        Route::put('/bank', [CompanyController::class, 'updateBank'])->name('bank.update');
        Route::put('/preferences', [CompanyController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/store', [CompanyController::class, 'store'])->name('store');
        Route::get('/switch', [CompanyController::class, 'switch'])->name('switch');
        Route::get('/switch/{id}', [CompanyController::class, 'switchTo'])->name('switch-to');
    });

    // App Settings
    Route::prefix('settings')->group(function () {

        Route::get('/', [SettingController::class, 'index'])
            ->name('settings.index');

        Route::post('/update', [SettingController::class, 'update'])
            ->name('settings.update');

        Route::post('/upload-logo', [SettingController::class, 'uploadLogo'])
            ->name('settings.upload.logo');

        Route::post('/upload-signature', [SettingController::class, 'uploadSignature'])
            ->name('settings.upload.signature');

        Route::delete('/logo/remove', [SettingController::class, 'removeMedia'])
            ->name('settings.logo.remove');

        Route::post('/remove-media', [SettingController::class, 'removeMedia'])
            ->name('settings.remove.media');
    });

    // Client Management
    Route::prefix('clients')
        ->middleware(['company.selected'])
        ->group(function () {

            // CRUD Routes
            Route::get('/', [ClientController::class, 'index'])
                ->name('clients.index');

            Route::get('/create', [ClientController::class, 'create'])
                ->name('clients.create');

            Route::post('/', [ClientController::class, 'store'])
                ->name('clients.store');

            Route::get('/search', [ClientController::class, 'search'])
                ->name('clients.search');

            Route::get('/filter/state', [ClientController::class, 'filterByState'])
                ->name('clients.filter.state');

            Route::get('/filter/status', [ClientController::class, 'filterByStatus'])
                ->name('clients.filter.status');

            Route::get('/{client}', [ClientController::class, 'show'])
                ->name('clients.show');

            Route::get('/{client}/edit', [ClientController::class, 'edit'])
                ->name('clients.edit');

            Route::put('/{client}', [ClientController::class, 'update'])
                ->name('clients.update');

            Route::delete('/{client}', [ClientController::class, 'destroy'])
                ->name('clients.destroy');
        });


    // Product Management
    Route::prefix('products')
        ->middleware(['company.selected'])
        ->name('products.')
        ->group(function () {
            // Product search (AJAX) – used by invoice builder
            Route::get('/search', [ProductController::class, 'search'])->name('search');
            // Product stock info (AJAX) – returns stock/deduction details
            Route::get('/{product}/stock-info', [ProductController::class, 'stockInfo'])->name('stock-info');
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/search', [ProductController::class, 'search'])->name('search');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

            // New Routes
            Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])
                ->name('adjust-stock');

            Route::get('/{product}/stock-history', [ProductController::class, 'stockHistory'])
                ->name('stock-history');

            Route::get('/import', [ProductController::class, 'import'])
                ->name('import');

            Route::post('/import', [ProductController::class, 'processImport'])
                ->name('import.process');

            Route::get('/export', [ProductController::class, 'export'])
                ->name('export');
        });
    // ============================================
    // PHASE 4A: PROFORMA INVOICES
    // ============================================
    Route::prefix('proformas')
        ->middleware(['company.selected'])
        ->name('proformas.')
        ->group(function () {

            Route::get('/', [ProformaController::class, 'index'])
                ->name('index');

            Route::get('/create', [ProformaController::class, 'create'])
                ->name('create');

            Route::post('/', [ProformaController::class, 'store'])
                ->middleware('throttle:invoice-create')
                ->name('store');

            Route::get('/{id}', [ProformaController::class, 'show'])
                ->name('show')
                ->where('id', '[0-9]+');

            Route::get('/{id}/edit', [ProformaController::class, 'edit'])
                ->name('edit')
                ->where('id', '[0-9]+');

            Route::put('/{id}', [ProformaController::class, 'update'])
                ->name('update')
                ->where('id', '[0-9]+');

            Route::delete('/{id}', [ProformaController::class, 'destroy'])
                ->middleware('throttle:invoice-delete')
                ->name('destroy')
                ->where('id', '[0-9]+');

            Route::post('/proformas/{id}/convert-to-gst', [ProformaController::class, 'convertToGst'])
                ->name('proformas.convert-to-gst');

            Route::get('/proformas/{id}/preview', [ProformaController::class, 'stream'])->name('proformas.preview');

            Route::get('/{id}/pdf', [ProformaController::class, 'pdf'])
                ->name('pdf')
                ->where('id', '[0-9]+');

            Route::post('/proformas/{id}/send-email', [ProformaController::class, 'sendEmail'])->name('proformas.send-email');
        });


    // ============================================
    // PHASE 4B: GST INVOICES
    // ============================================
    Route::prefix('gst-invoices')
        ->middleware(['company.selected'])
        ->name('gst-invoices.')
        ->group(function () {

            Route::get('/', [GSTInvoiceController::class, 'index'])
                ->name('index');

            Route::get('/create', [GSTInvoiceController::class, 'create'])
                ->name('create');

            Route::post('/', [GSTInvoiceController::class, 'store'])
                ->middleware('throttle:invoice-create')
                ->name('store');

            Route::get('/{id}', [GSTInvoiceController::class, 'show'])
                ->name('show')
                ->where('id', '[0-9]+');

            Route::get('/{id}/edit', [GSTInvoiceController::class, 'edit'])
                ->name('edit')
                ->where('id', '[0-9]+');

            Route::put('/{id}', [GSTInvoiceController::class, 'update'])
                ->name('update')
                ->where('id', '[0-9]+');

            Route::delete('/{id}', [GSTInvoiceController::class, 'destroy'])
                ->middleware('throttle:invoice-delete')
                ->name('destroy')
                ->where('id', '[0-9]+');

            Route::get('/gst-invoices/{id}/preview', [GSTInvoiceController::class, 'stream'])->name('gst-invoices.preview');

            Route::get('/{id}/pdf', [GSTInvoiceController::class, 'pdf'])
                ->name('pdf')
                ->where('id', '[0-9]+');

            Route::post('/gst-invoices/bulk-pdf', [GSTInvoiceController::class, 'bulkPdf'])->name('gst-invoices.bulk-pdf');
            Route::post('/gst-invoices/{id}/send-email', [GSTInvoiceController::class, 'sendEmail'])->name('gst-invoices.send-email');
        });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/outstanding', [ReportController::class, 'outstanding'])->name('outstanding');
        Route::get('/gstr1', [ReportController::class, 'gstr1'])->name('gstr1');
        Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// ============================================
// SUPER ADMIN ROUTES
// ============================================
Route::middleware(['auth', 'verified', 'super.admin'])->prefix('super-admin')->name('super-admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Companies
    Route::get('/companies', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'index'])->name('companies');
    Route::get('/companies/{company}', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'show'])->name('companies.show');
    Route::post('/companies/{company}/approve', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'approve'])->name('companies.approve');
    Route::post('/companies/{company}/suspend', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'suspend'])->name('companies.suspend');

    // Users
    Route::get('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users');
    Route::get('/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/suspend', [\App\Http\Controllers\SuperAdmin\UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/approve', [\App\Http\Controllers\SuperAdmin\UserController::class, 'approve'])->name('users.approve');

    // Invoices
    Route::get('/invoices', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'index'])->name('invoices');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\SuperAdmin\InvoiceController::class, 'show'])->name('invoices.show');

    // Proformas
    Route::get('/proformas', [\App\Http\Controllers\SuperAdmin\ProformaController::class, 'index'])->name('proformas');
    Route::get('/proformas/{invoice}', [\App\Http\Controllers\SuperAdmin\ProformaController::class, 'show'])->name('proformas.show');

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'index'])->name('analytics');

    // Subscriptions
    Route::get('/subscriptions', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'index'])->name('subscriptions');

    // Logs
    Route::get('/logs', [\App\Http\Controllers\SuperAdmin\LogController::class, 'index'])->name('logs');

    // Audit
    Route::get('/audit', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'index'])->name('audit');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('profile.update');

    // Company Users
    Route::get('/companies/{company}/users', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'users'])->name('companies.users');

    // Company Invoices
    Route::get('/companies/{company}/invoices', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'invoices'])->name('companies.invoices');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');
});

// Staff Invite Routes
Route::middleware(['auth', 'verified', 'company.selected'])->group(function () {
    Route::get('/staff/invite', [App\Http\Controllers\StaffController::class, 'inviteForm'])->name('staff.invite.form');
    Route::post('/staff/invite', [App\Http\Controllers\StaffController::class, 'sendInvite'])->name('staff.invite.send');
    //Route::post('/staff/invite/{id}/resend', [App\Http\Controllers\StaffController::class, 'resendInvite'])->name('staff.invite.resend');
    //Route::delete('/staff/invite/{id}/cancel', [App\Http\Controllers\StaffController::class, 'cancelInvite'])->name('staff.invite.cancel');
});

// Accept Invite (no company needed)
Route::get('/invite/{token}', [App\Http\Controllers\StaffController::class, 'acceptInvite'])->name('invite.accept');
Route::post('/invite/{token}/register', [App\Http\Controllers\StaffController::class, 'registerFromInvite'])->name('invite.register');

require __DIR__ . '/auth.php';
