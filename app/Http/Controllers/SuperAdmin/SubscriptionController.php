<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Plan monthly prices (can be moved to config/database later)
        $priceMap = [
            'trial'      => 0,
            'basic'      => 999,
            'premium'    => 2499,
            'enterprise' => 4999,
        ];

        $activeCount = Company::where('is_active', true)->count();
        $trialCount  = Company::where('subscription_plan', 'trial')->count();

        $activeCompanies = Company::where('is_active', true)->get(['id', 'subscription_plan']);

        // Build plan stats dynamically
        $planStats = collect($priceMap)->map(function ($price, $plan) use ($activeCompanies) {
            $count = $activeCompanies->where('subscription_plan', $plan)->count();
            return [
                'name'     => ucfirst($plan),
                'plan_key' => $plan,
                'price'    => $price,
                'count'    => $count,
                'revenue'  => $count * $price,
            ];
        })->values();

        $monthlyRevenue = $planStats->sum('revenue');

        return view('super-admin.subscriptions.index', compact(
            'activeCount',
            'trialCount',
            'monthlyRevenue',
            'planStats'
        ));
    }
}
