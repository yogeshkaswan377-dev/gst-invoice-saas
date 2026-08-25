@extends('layouts.app')

@section('title', 'Dashboard - GST Billing Pro')
@section('meta_description', 'GST Billing Pro Dashboard - Track invoices, revenue, clients, and GST compliance in real-time.')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="rounded-4 shadow p-4 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="mb-2 fw-bold">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                    <p class="mb-0 opacity-75">
                        @if(isset($company))
                        {{ $company->name }} — {{ $company->gstin ?? 'GSTIN not set' }}
                        @else
                        Complete your company profile to get started.
                        @endif
                    </p>
                </div>
                <a href="{{ route('gst-invoices.create') }}" class="btn btn-light fw-semibold mt-2 mt-md-0" style="border-radius:12px; padding:10px 20px;">
                    <i class="fas fa-plus-circle me-2"></i> Create Invoice
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row mb-4">
    {{-- Total Invoices --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Invoices</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalInvoices ?? 0 }}</h3>
                </div>
                <div>
                    <i class="fas fa-file-invoice fa-2x text-primary opacity-25"></i>
                </div>
            </div>
            @if(isset($invoiceGrowth))
            <small class="text-muted mt-2 d-block">
                <i class="fas {{ $invoiceGrowth >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger' }}"></i>
                <span class="{{ $invoiceGrowth >= 0 ? 'text-success' : 'text-danger' }}">{{ abs($invoiceGrowth) }}%</span> from last month
            </small>
            @endif
        </div>
    </div>

    {{-- Total Revenue --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Revenue</h6>
                    <h3 class="mb-0 fw-bold">₹{{ number_format($totalRevenue ?? 0) }}</h3>
                </div>
                <div>
                    <i class="fas fa-rupee-sign fa-2x text-success opacity-25"></i>
                </div>
            </div>
            @if(isset($revenueGrowth))
            <small class="text-muted mt-2 d-block">
                <i class="fas {{ $revenueGrowth >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger' }}"></i>
                <span class="{{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">{{ abs($revenueGrowth) }}%</span> from last month
            </small>
            @endif
        </div>
    </div>

    {{-- Pending Amount --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Pending Amount</h6>
                    <h3 class="mb-0 fw-bold">₹{{ number_format($pendingAmount ?? 0) }}</h3>
                </div>
                <div>
                    <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
                </div>
            </div>
            @if(isset($overdueCount) && $overdueCount > 0)
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-exclamation-circle text-danger"></i>
                <span class="text-danger">{{ $overdueCount }} invoices</span> overdue
            </small>
            @endif
        </div>
    </div>

    {{-- Total Clients --}}
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Clients</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalClients ?? 0 }}</h3>
                </div>
                <div>
                    <i class="fas fa-users fa-2x opacity-25" style="color:#8b5cf6;"></i>
                </div>
            </div>
            @if(isset($newClientsThisMonth) && $newClientsThisMonth > 0)
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-user-plus text-success"></i>
                <span class="text-success">+{{ $newClientsThisMonth }}</span> new this month
            </small>
            @endif
        </div>
    </div>
</div>

{{-- Inventory Stats Row --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #6366f1;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Products</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalProducts ?? 0 }}</h3>
                </div>
                <div>
                    <i class="fas fa-box fa-2x opacity-25" style="color:#6366f1;"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Active products in inventory</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Current Stock</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalStock ?? 0, 2) }} Mtr</h3>
                </div>
                <div>
                    <i class="fas fa-ruler-combined fa-2x opacity-25" style="color:#10b981;"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Total fabric available</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Low Stock</h6>
                    <h3 class="mb-0 fw-bold">{{ $lowStockCount ?? 0 }}</h3>
                </div>
                <div>
                    <i class="fas fa-exclamation-triangle fa-2x opacity-25" style="color:#f59e0b;"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Items below minimum level</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Out of Stock</h6>
                    <h3 class="mb-0 fw-bold">{{ $outOfStockCount ?? 0 }}</h3>
                </div>
                <div>
                    <i class="fas fa-times-circle fa-2x opacity-25" style="color:#ef4444;"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Products with zero stock</small>
        </div>
    </div>
</div>

{{-- Consumption & Low Stock Table --}}
<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fas fa-chart-line me-2 text-info"></i>Today's Consumption</h5>
                <h2 class="fw-bold">{{ number_format($todayConsumption ?? 0, 2) }} Mtr</h2>
                <small class="text-muted">Fabric consumed today via invoices</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fas fa-exclamation-circle me-2 text-warning"></i>Low Stock Products</h5>
                @if(isset($lowStockProducts) && $lowStockProducts->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($lowStockProducts as $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        {{ $product->name }}
                        <span class="badge bg-warning text-dark">{{ $product->stock }} {{ $product->stock_unit }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted">All products are sufficiently stocked.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Invoice Status Cards --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-4">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h4 class="mb-1 fw-bold">{{ $paidCount ?? 0 }}</h4>
                <small class="text-muted fw-medium">Paid</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-4">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h4 class="mb-1 fw-bold">{{ $pendingCount ?? 0 }}</h4>
                <small class="text-muted fw-medium">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                <h4 class="mb-1 fw-bold">{{ $overdueCount ?? 0 }}</h4>
                <small class="text-muted fw-medium">Overdue</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-4">
                <i class="fas fa-file-alt fa-2x text-info mb-2"></i>
                <h4 class="mb-1 fw-bold">{{ $draftCount ?? 0 }}</h4>
                <small class="text-muted fw-medium">Draft</small>
            </div>
        </div>
    </div>
</div>

{{-- Charts + Recent Invoices --}}
<div class="row">
    {{-- Revenue Chart --}}
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line text-primary me-2"></i> Revenue Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="280"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Clients --}}
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-star text-warning me-2"></i> Top Clients</h5>
            </div>
            <div class="card-body">
                @forelse($topClients ?? [] as $client)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 fw-semibold">{{ $client->name }}</h6>
                        <small class="text-muted">{{ $client->invoices_count }} invoices</small>
                    </div>
                    <span class="badge rounded-pill" style="background:linear-gradient(135deg, #dbeafe, #ede9fe); color:#1e3a8a; font-size:13px; padding:6px 14px;">
                        ₹{{ number_format($client->total_revenue) }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-4">No client data yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Recent Invoices Table --}}
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-info"></i> Recent Invoices</h5>
                <a href="{{ route('gst-invoices.index') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; padding:8px 16px;">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:0.05em;">
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices ?? [] as $invoice)
                            <tr>
                                <td class="fw-semibold">{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->client->name ?? 'N/A' }}</td>
                                <td class="fw-semibold">₹{{ number_format($invoice->grand_total) }}</td>
                                <td>
                                    @if($invoice->status == 'paid')
                                    <span class="badge rounded-pill" style="background:#d1fae5; color:#065f46; padding:5px 12px;">Paid</span>
                                    @elseif($invoice->status == 'pending')
                                    <span class="badge rounded-pill" style="background:#fef3c7; color:#92400e; padding:5px 12px;">Pending</span>
                                    @elseif($invoice->status == 'overdue')
                                    <span class="badge rounded-pill" style="background:#fee2e2; color:#991b1b; padding:5px 12px;">Overdue</span>
                                    @else
                                    <span class="badge rounded-pill" style="background:#e2e8f0; color:#475569; padding:5px 12px;">Draft</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('gst-invoices.show', $invoice) }}" class="btn btn-sm" style="background:#dbeafe; color:#1d4ed8; border-radius:8px; padding:4px 10px;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2 opacity-25"></i>
                                    No invoices yet. <a href="{{ route('gst-invoices.create') }}">Create your first invoice</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-bolt text-warning me-2"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('gst-invoices.create') }}" class="btn w-100 mb-2 text-start text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 16px;">
                    <i class="fas fa-file-invoice me-2"></i> Create GST Invoice
                </a>
                <a href="{{ route('proformas.create') }}" class="btn w-100 mb-2 text-start" style="background:#f1f5f9; border-radius:12px; padding:10px 16px; color:#0f172a;">
                    <i class="fas fa-file-alt me-2"></i> Create Proforma
                </a>
                <a href="{{ route('clients.create') }}" class="btn w-100 mb-2 text-start" style="background:#f1f5f9; border-radius:12px; padding:10px 16px; color:#0f172a;">
                    <i class="fas fa-user-plus me-2"></i> Add New Client
                </a>
                <a href="{{ route('reports.gstr1') }}" class="btn w-100 text-start" style="background:#f1f5f9; border-radius:12px; padding:10px 16px; color:#0f172a;">
                    <i class="fas fa-download me-2"></i> Download GSTR-1
                </a>
            </div>
        </div>

        {{-- Recent Clients --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-user-plus text-success me-2"></i> Recent Clients</h5>
                <a href="{{ route('clients.index') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; padding:8px 16px;">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentClients ?? [] as $client)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 fw-semibold">{{ $client->name }}</h6>
                        <small class="text-muted" style="font-size:11px;">{{ $client->gstin ?? 'No GSTIN' }}</small>
                    </div>
                    <span class="badge rounded-pill" style="background:#f1f5f9; color:#64748b; font-size:11px;">
                        {{ $client->city ?? 'N/A' }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center py-4">No clients added yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var canvas = document.getElementById('revenueChart');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');

        // Safe data - default if empty
        var chartLabels = @json($chartLabels ?? []);
        var chartData = @json($chartData ?? []);

        // Clean data
        var cleanData = [];
        for (var i = 0; i < chartData.length; i++) {
            cleanData.push(parseFloat(chartData[i]) || 0);
        }

        // If all zero, show sample data for display
        var hasData = false;
        for (var j = 0; j < cleanData.length; j++) {
            if (cleanData[j] > 0) hasData = true;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels.length > 0 ? chartLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: cleanData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#1e3a8a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
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
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                return '₹' + (v / 1000).toFixed(0) + 'K';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush