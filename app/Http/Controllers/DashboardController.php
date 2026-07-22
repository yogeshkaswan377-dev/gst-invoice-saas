<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = session('current_company_id')
            ?? auth()->user()->current_company_id
            ?? Company::first()?->id;

        $company = Company::find($companyId);

        // Total Invoices
        $totalInvoices = Invoice::where('company_id', $companyId)->count();
        $lastMonthInvoices = Invoice::where('company_id', $companyId)
            ->whereMonth('invoice_date', Carbon::now()->subMonth()->month)
            ->whereYear('invoice_date', Carbon::now()->subMonth()->year)
            ->count();
        $thisMonthInvoices = Invoice::where('company_id', $companyId)
            ->whereMonth('invoice_date', Carbon::now()->month)
            ->whereYear('invoice_date', Carbon::now()->year)
            ->count();
        $invoiceGrowth = $lastMonthInvoices > 0
            ? round((($thisMonthInvoices - $lastMonthInvoices) / $lastMonthInvoices) * 100)
            : ($thisMonthInvoices > 0 ? 100 : 0);

        // Total Revenue
        $totalRevenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')->sum('grand_total');

        $lastMonthRevenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereMonth('invoice_date', Carbon::now()->subMonth()->month)
            ->whereYear('invoice_date', Carbon::now()->subMonth()->year)
            ->sum('grand_total');

        $thisMonthRevenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereMonth('invoice_date', Carbon::now()->month)
            ->whereYear('invoice_date', Carbon::now()->year)
            ->sum('grand_total');

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Pending Amount
        $pendingAmount = Invoice::where('company_id', $companyId)
            ->whereIn('status', ['sent', 'viewed', 'accepted'])
            ->sum('balance_due');

        // Invoice Status Counts
        $paidCount = Invoice::where('company_id', $companyId)->where('status', 'paid')->count();
        $pendingCount = Invoice::where('company_id', $companyId)->whereIn('status', ['sent', 'viewed', 'accepted'])->count();
        $overdueCount = Invoice::where('company_id', $companyId)->where('status', 'overdue')->count();
        $draftCount = Invoice::where('company_id', $companyId)->where('status', 'draft')->count();

        // Total Clients
        $totalClients = Client::where('company_id', $companyId)->count();
        $newClientsThisMonth = Client::where('company_id', $companyId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Top Clients — RAW QUERY (no relationship needed)
        $topClients = DB::table('clients')
            ->leftJoin('invoices', function ($join) use ($companyId) {
                $join->on('clients.id', '=', 'invoices.client_id')
                    ->where('invoices.status', '=', 'paid')
                    ->whereNull('invoices.deleted_at');
            })
            ->select(
                'clients.id',
                'clients.name',
                DB::raw('COUNT(invoices.id) as invoices_count'),
                DB::raw('COALESCE(SUM(invoices.grand_total), 0) as total_revenue')
            )
            ->where('clients.company_id', $companyId)

            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // Recent Invoices
        $recentInvoices = Invoice::where('company_id', $companyId)
            ->with('client')
            ->latest('invoice_date')
            ->limit(5)
            ->get();

        // Recent Clients
        $recentClients = Client::where('company_id', $companyId)
            ->latest()
            ->limit(4)
            ->get();

        // Chart Data — Last 6 months
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->format('M');

            $sum = Invoice::where('company_id', $companyId)
                ->where('status', 'paid')
                ->whereYear('invoice_date', $month->year)
                ->whereMonth('invoice_date', $month->month)
                ->sum('grand_total');

            // 👇 FORCE FLOAT (not int, not string)
            $chartData[] = (float) $sum;
        }

        // ────────── Inventory Stats ──────────
        $totalProducts = Product::where('company_id', $companyId)->where('is_active', true)->count();
        $totalStock = Product::where('company_id', $companyId)->sum('stock');
        $lowStockCount = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->count();
        $outOfStockCount = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('stock', '<=', 0)
            ->count();
        $todayConsumption = StockHistory::where('company_id', $companyId)
            ->whereDate('created_at', Carbon::today())
            ->where('action', 'invoice_create')   // or whatever action string you use for invoice deductions
            ->sum('consumed_stock');

        $lowStockProducts = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'company',
            'totalInvoices',
            'invoiceGrowth',
            'totalRevenue',
            'revenueGrowth',
            'pendingAmount',
            'paidCount',
            'pendingCount',
            'overdueCount',
            'draftCount',
            'totalClients',
            'newClientsThisMonth',
            'topClients',
            'recentInvoices',
            'recentClients',
            'chartLabels',
            'chartData',
            // new inventory variables
            'totalProducts',
            'totalStock',
            'lowStockCount',
            'outOfStockCount',
            'todayConsumption',
            'lowStockProducts'
        ));
    }
}
