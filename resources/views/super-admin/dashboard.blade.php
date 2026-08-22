@extends('layouts.super-admin')

@section('title', 'System Overview')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Global Infrastructure Node</h2>
        <p class="text-sm text-gray-500 mt-1">Real-time tenant monitoring, revenue streams, and system health.</p>
    </div>
    <div class="flex items-center gap-3">
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-50 shadow-sm transition">
            <i class="fa-solid fa-download"></i> Export Report
        </button>
        <!-- 
        <a href="/super-admin/companies/create" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-sm font-semibold text-white rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-600/10 transition">
            <i class="fa-solid fa-plus"></i> New Company
        </a>
        -->
    </div>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Total Companies -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60 flex items-start justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Tenants</span>
            <h3 class="text-3xl font-bold tracking-tight text-gray-900">{{ number_format($totalCompanies ?? 0) }}</h3>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                <i class="fa-solid fa-trend-up"></i> +{{ $newThisMonth ?? 0 }}
            </span>
        </div>
        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
            <i class="fa-solid fa-building text-xl"></i>
        </div>
    </div>

    <!-- Active Subscriptions -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60 flex items-start justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Active Subscriptions</span>
            <h3 class="text-3xl font-bold tracking-tight text-gray-900">{{ number_format($activeSubscriptions ?? 0) }}</h3>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                <i class="fa-solid fa-circle-check"></i> {{ $activePercentage ?? 0 }}% active
            </span>
        </div>
        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
            <i class="fa-solid fa-crown text-xl"></i>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60 flex items-start justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Monthly Revenue</span>
            <h3 class="text-3xl font-bold tracking-tight text-gray-900">₹{{ number_format($monthlyRevenue ?? 0, 0) }}</h3>
            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ ($revenueGrowth ?? 0) >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-2 py-0.5 rounded-md">
                <i class="fa-solid fa-{{ ($revenueGrowth ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}"></i> {{ abs($revenueGrowth ?? 0) }}%
            </span>
        </div>
        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
            <i class="fa-solid fa-coins text-xl"></i>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60 flex items-start justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Platform Users</span>
            <h3 class="text-3xl font-bold tracking-tight text-gray-900">{{ number_format($totalUsers ?? 0) }}</h3>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                <i class="fa-solid fa-user-plus"></i> +{{ $newUsersToday ?? 0 }} today
            </span>
        </div>
        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
            <i class="fa-solid fa-users text-xl"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    <!-- Revenue Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Revenue Stream</h3>
            <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                <button class="px-3 py-1 text-xs rounded-md bg-white text-gray-700 font-medium shadow-sm">Monthly</button>
            </div>
        </div>
        <div style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Companies Growth -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Tenant Growth</h3>
        </div>
        <div style="height: 300px;">
            <canvas id="growthChart"></canvas>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6 mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Control Panel</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="/super-admin/companies" class="p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition text-center group">
            <i class="fa-solid fa-building text-emerald-600 text-xl mb-1 block group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-medium text-gray-700">View Companies</span>
        </a>
        <a href="/super-admin/subscriptions" class="p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition text-center group">
            <i class="fa-solid fa-crown text-emerald-600 text-xl mb-1 block group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-medium text-gray-700">Manage Plans</span>
        </a>
        <a href="/super-admin/users" class="p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition text-center group">
            <i class="fa-solid fa-user-shield text-amber-600 text-xl mb-1 block group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-medium text-gray-700">Manage Users</span>
        </a>
        <a href="/super-admin/logs" class="p-4 bg-slate-100 rounded-xl hover:bg-slate-200 transition text-center group">
            <i class="fa-solid fa-terminal text-slate-600 text-xl mb-1 block group-hover:scale-110 transition-transform"></i>
            <span class="text-sm font-medium text-gray-700">System Logs</span>
        </a>
    </div>
</div>

<!-- Recent Companies Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Recent Tenants</h3>
            <p class="text-xs text-gray-500 mt-0.5">Latest companies registered on the platform.</p>
        </div>
        <a href="/super-admin/companies" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">View All →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/70 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4">Company</th>
                    <th class="px-6 py-4">GSTIN</th>
                    <th class="px-6 py-4">Plan</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Created</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-600">
                @forelse($recentCompanies ?? [] as $company)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-xl font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($company->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $company->name }}</h4>
                                <p class="text-xs text-gray-400">{{ $company->email ?? 'No email' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $company->gstin ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700">
                            {{ ucfirst($company->subscription_plan ?? 'Trial') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ $company->is_active ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }} px-2.5 py-0.5 rounded-full ring-1 {{ $company->is_active ? 'ring-emerald-600/10' : 'ring-rose-600/10' }}">
                            {{ $company->is_active ? 'Active' : 'Suspended' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $company->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex items-center gap-1">
                            <a href="/super-admin/companies/{{ $company->id }}" class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-gray-50 transition">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="/super-admin/companies/{{ $company->id }}/users" class="p-2 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-gray-50 transition">
                                <i class="fa-solid fa-users"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-inbox text-2xl block mb-2 opacity-50"></i>
                        No companies registered yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Bottom Grid: Health + Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Platform Health -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">System Health</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50">
                <span class="text-sm font-medium text-gray-700"><i class="fa-solid fa-check-circle text-emerald-500 mr-2"></i>API Gateway</span>
                <span class="text-xs font-semibold text-emerald-700">Operational</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50">
                <span class="text-sm font-medium text-gray-700"><i class="fa-solid fa-check-circle text-emerald-500 mr-2"></i>Database Cluster</span>
                <span class="text-xs font-semibold text-emerald-700">Healthy</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50">
                <span class="text-sm font-medium text-gray-700"><i class="fa-solid fa-exclamation-circle text-amber-500 mr-2"></i>Queue Worker</span>
                <span class="text-xs font-semibold text-amber-700">Processing ({{ $queueJobs ?? 0 }})</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50">
                <span class="text-sm font-medium text-gray-700"><i class="fa-solid fa-check-circle text-emerald-500 mr-2"></i>Storage Node</span>
                <span class="text-xs font-semibold text-emerald-700">Active</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Activity Stream</h3>
        <div class="space-y-3">
            @forelse($recentCompanies ?? [] as $company)
            <div class="flex items-start gap-3 text-sm">
                <div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 flex-shrink-0"></div>
                <div>
                    <p class="text-gray-700">New company <strong>{{ $company->name }}</strong> registered</p>
                    <p class="text-xs text-gray-400">{{ $company->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="flex items-start gap-3 text-sm">
                <div class="w-2 h-2 rounded-full bg-emerald-500 mt-2 flex-shrink-0"></div>
                <div>
                    <p class="text-gray-700">System monitoring active</p>
                    <p class="text-xs text-gray-400">All systems operational</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.chartData = {
        labels: @json($chartLabels ?? []),
        revenue: @json($chartData ?? []),
        growth: @json($growthData ?? [])
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        var revCanvas = document.getElementById('revenueChart');
        if (revCanvas) {
            new Chart(revCanvas, {
                type: 'line',
                data: {
                    labels: window.chartData.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: window.chartData.revenue,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.05)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: v => '₹' + (v / 1000).toFixed(0) + 'K'
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Growth Chart
        var growCanvas = document.getElementById('growthChart');
        if (growCanvas) {
            new Chart(growCanvas, {
                type: 'bar',
                data: {
                    labels: window.chartData.labels,
                    datasets: [{
                        label: 'New Companies',
                        data: window.chartData.growth,
                        backgroundColor: '#a5b4fc',
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush