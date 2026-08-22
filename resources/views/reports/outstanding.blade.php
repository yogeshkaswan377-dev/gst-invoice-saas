@extends('layouts.app')

@section('title', 'Outstanding Payments - GST Billing Pro')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 style="font-size:18px; font-weight:700; margin:0;">Outstanding Payments</h2>
        <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Track overdue and pending invoices</p>
    </div>
    <a href="{{ route('reports.gstr1') }}" class="btn btn-outline" style="border-radius:10px; font-weight:600; font-size:13px;">
        <i class="fas fa-file-invoice me-1"></i> View GSTR-1
    </a>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="text-muted mb-2">Total Outstanding</h6>
            <h3 class="mb-0 fw-bold">₹{{ number_format($totalOutstanding) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="text-muted mb-2">Overdue Amount</h6>
            <h3 class="mb-0 fw-bold">₹{{ number_format($totalOverdue) }}</h3>
            <small class="text-danger">{{ $invoices->where('due_date', '<', now())->count() }} overdue invoices</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="text-muted mb-2">Avg Days Overdue</h6>
            <h3 class="mb-0 fw-bold">{{ $avgDaysOverdue }}</h3>
            <small class="text-muted">days</small>
        </div>
    </div>
</div>

{{-- Outstanding Invoices Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.05em; background:#f8fafc;">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Client</th>
                        <th>Due Date</th>
                        <th>Balance Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->client->name ?? 'N/A' }}</td>
                        <td>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</td>
                        <td class="fw-bold">₹{{ number_format($invoice->balance_due) }}</td>
                        <td>
                            <span class="badge badge-status badge-overdue">Overdue</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No outstanding invoices</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection