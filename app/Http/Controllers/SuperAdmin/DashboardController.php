<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // SuperAdmin/DashboardController.php

    public function index()
    {
        // Last 6 months for charts
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->startOfMonth());
        }

        $chartLabels = $months->map(fn($m) => $m->format('M Y'))->toArray();

        $chartData = $months->map(
            fn($m) =>
            Invoice::whereBetween('created_at', [$m, $m->copy()->endOfMonth()])->sum('grand_total')
        )->toArray();

        $growthData = $months->map(
            fn($m) =>
            Company::whereBetween('created_at', [$m, $m->copy()->endOfMonth()])->count()
        )->toArray();

        // Revenue growth: current month vs last month
        $currentMonthRevenue = Invoice::whereMonth('created_at', now()->month)->sum('grand_total');
        $lastMonthRevenue = Invoice::whereMonth('created_at', now()->subMonth()->month)->sum('grand_total');
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : 0;

        return view('super-admin.dashboard', [
            'totalCompanies'      => Company::count(),
            'newThisMonth'        => Company::whereMonth('created_at', now()->month)->count(),
            'activeSubscriptions' => Company::where('is_active', true)->count(),
            'activePercentage'    => Company::count() > 0 ? round((Company::where('is_active', true)->count() / Company::count()) * 100) : 0,
            'monthlyRevenue'      => $currentMonthRevenue,
            'revenueGrowth'       => $revenueGrowth,
            'totalUsers'          => User::count(),
            'newUsersToday'       => User::whereDate('created_at', today())->count(),
            'recentCompanies'     => Company::latest()->limit(5)->get(),
            'chartLabels'         => $chartLabels,
            'chartData'           => $chartData,
            'growthData'          => $growthData,
            'recentActivities'    => Company::latest()->limit(4)->get(),
            'queueJobs'           => DB::table('jobs')->count(),
        ]);
    }
}
