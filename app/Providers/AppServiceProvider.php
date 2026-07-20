<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Company;
use App\Policies\InvoicePolicy;
use App\Policies\ClientPolicy;
use App\Policies\CompanyPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Services\GST\GSTCalculationService;
use App\Services\GST\TaxBreakdownService;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\Contracts\StockHistoryRepositoryInterface;
use App\Repositories\StockHistoryRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // GST Services
        $this->app->singleton(GSTCalculationService::class, function ($app) {
            return new GSTCalculationService();
        });

        $this->app->singleton(TaxBreakdownService::class, function ($app) {
            return new TaxBreakdownService($app->make(GSTCalculationService::class));
        });

        // Invoice Repository
        $this->app->bind(
            InvoiceRepositoryInterface::class,
            InvoiceRepository::class
        );

        // Product Repository
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        // Stock History Repository
        $this->app->bind(
            StockHistoryRepositoryInterface::class,
            StockHistoryRepository::class
        );
    }

    public function boot(): void
    {
        // Rate Limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Paid plan - 120 requests per minute
        RateLimiter::for('api-premium', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('invoice-create', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('invoice-delete', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
    }
}
