@extends('layouts.super-admin')

@section('title', 'Subscriptions')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Subscription Plans</h1>
        <p class="text-xs text-gray-500 mt-1">Real-time overview of tenant subscriptions.</p>
    </div>
    {{-- Removed "Add Plan" button --}}
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Active Subscriptions</span>
        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($activeCount ?? 0) }}</h3>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Monthly Revenue</span>
        <h3 class="text-3xl font-bold text-gray-900 mt-1">₹{{ number_format($monthlyRevenue ?? 0) }}</h3>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Trial Running</span>
        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($trialCount ?? 0) }}</h3>
    </div>
</div>

{{-- Plans Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/70 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4">Plan</th>
                    <th class="px-6 py-4">Price</th>
                    <th class="px-6 py-4">Companies</th>
                    <th class="px-6 py-4">Revenue</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-600">
                @forelse($planStats ?? [] as $plan)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $plan['name'] }}</td>
                    <td class="px-6 py-4">
                        ₹{{ number_format($plan['price']) }}/mo
                    </td>
                    <td class="px-6 py-4">{{ $plan['count'] }}</td>
                    <td class="px-6 py-4 font-semibold">₹{{ number_format($plan['revenue']) }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full ring-1 ring-emerald-600/10">
                            Active
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">No subscription plans found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection