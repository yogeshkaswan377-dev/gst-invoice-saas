<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $year = now()->year;

        // Monthly revenue data
        $monthlyRevenueRaw = Invoice::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(grand_total) as total')
        )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Monthly signups
        $monthlyUsersRaw = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Fill arrays with 12 zeroes
        $revenueByMonth = array_fill(0, 12, 0);
        $usersByMonth   = array_fill(0, 12, 0);

        foreach ($monthlyRevenueRaw as $row) {
            $revenueByMonth[$row->month - 1] = (float) $row->total;
        }

        foreach ($monthlyUsersRaw as $row) {
            $usersByMonth[$row->month - 1] = (int) $row->total;
        }

        // Companies by plan
        $companiesByPlan = Company::select('subscription_plan', DB::raw('count(*) as count'))
            ->groupBy('subscription_plan')
            ->get();

        $planLabels = $companiesByPlan->pluck('subscription_plan')
            ->map(fn($plan) => ucfirst($plan ?? 'Trial'))
            ->toArray();

        $planCounts = $companiesByPlan->pluck('count')->toArray();

        return view('super-admin.analytics', [
            'totalRevenue'         => Invoice::sum('grand_total'),
            'monthlyRevenueLabels' => $monthLabels,
            'monthlyRevenueData'   => $revenueByMonth,
            'monthlyUsersData'     => $usersByMonth,
            'planLabels'           => $planLabels,
            'planCounts'           => $planCounts,
            'totalCompanies'       => Company::count(),
            'totalUsers'           => User::count(),
            'totalInvoices'        => Invoice::count(),
        ]);
    }
}
