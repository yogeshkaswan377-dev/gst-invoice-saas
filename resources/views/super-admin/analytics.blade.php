@extends('layouts.super-admin')

@section('title', 'Analytics')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Platform Analytics</h2>
    <p class="text-sm text-gray-500 mt-1">Revenue analytics, user growth, and tenant distribution.</p>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Revenue</span>
        <h3 class="text-3xl font-bold text-gray-900 mt-2">₹{{ number_format($totalRevenue ?? 0) }}</h3>
        <i class="fa-solid fa-coins text-emerald-500 text-xl mt-2"></i>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Companies</span>
        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalCompanies ?? 0) }}</h3>
        <i class="fa-solid fa-building text-indigo-500 text-xl mt-2"></i>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Users</span>
        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalUsers ?? 0) }}</h3>
        <i class="fa-solid fa-users text-purple-500 text-xl mt-2"></i>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Invoices</span>
        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalInvoices ?? 0) }}</h3>
        <i class="fa-solid fa-file-invoice text-amber-500 text-xl mt-2"></i>
    </div>
</div>

{{-- Revenue & User Growth Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Monthly Revenue</h3>
        <div style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">User Growth</h3>
        <div style="height: 300px;">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>
</div>

{{-- Companies by Plan --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Tenants by Plan</h3>
    <div style="height: 300px; max-width: 500px;">
        <canvas id="planChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        var revCanvas = document.getElementById('revenueChart');
        if (revCanvas) {
            new Chart(revCanvas, {
                type: 'line',
                data: {
                    labels: @json($monthlyRevenueLabels ?? []),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($monthlyRevenueData ?? []),
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

        // User Growth Chart
        var userCanvas = document.getElementById('userGrowthChart');
        if (userCanvas) {
            new Chart(userCanvas, {
                type: 'bar',
                data: {
                    labels: @json($monthlyRevenueLabels ?? []),
                    datasets: [{
                        label: 'New Users',
                        data: @json($monthlyUsersData ?? []),
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

        // Plan Distribution Chart
        var planCanvas = document.getElementById('planChart');
        if (planCanvas) {
            new Chart(planCanvas, {
                type: 'doughnut',
                data: {
                    labels: @json($planLabels ?? []),
                    datasets: [{
                        data: @json($planCounts ?? []),
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    });
</script>
@endpush