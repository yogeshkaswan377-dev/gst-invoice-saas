@extends('layouts.app')

@section('title', 'GST Invoices - GST Billing Pro')
@section('meta_description', 'View, search and manage all your GST invoices. Filter by status, date, and client.')

@section('content')
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 style="font-size:18px; font-weight:700; margin:0;">GST Invoices</h2>
        <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Manage your tax invoices & bill of supply</p>
    </div>
    <a href="{{ route('gst-invoices.create') }}" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 20px; font-weight:600; text-decoration:none;">
        <i class="fas fa-plus-circle me-2"></i> Create Invoice
    </a>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('gst-invoices.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:12px;">Search</label>
                <input type="search" name="search" value="{{ request('search') }}" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0; padding:9px 14px;" placeholder="Invoice # or client...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size:12px;">Status</label>
                <select name="status" class="form-select" style="border-radius:10px; border:1px solid #e2e8f0;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Pending</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size:12px;">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size:12px;">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; font-weight:600;">
                    <i class="fas fa-filter me-1"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Invoices Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.05em; background:#f8fafc;">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices ?? [] as $invoice)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->client->name ?? 'N/A' }}</td>
                        <td>{{ $invoice->invoice_date?->format('d M Y') }}</td>
                        <td class="fw-bold">₹{{ number_format($invoice->grand_total ?? 0) }}</td>
                        <td>
                            <span class="badge-status badge-{{ $invoice->status === 'paid' ? 'paid' : ($invoice->status === 'overdue' ? 'overdue' : ($invoice->status === 'draft' ? 'draft' : 'pending')) }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('gst-invoices.show', $invoice) }}" class="btn btn-sm" style="background:#dbeafe; color:#1d4ed8; border-radius:8px; font-size:11px;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('gst-invoices.edit', $invoice) }}" class="btn btn-sm" style="background:#fef3c7; color:#92400e; border-radius:8px; font-size:11px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-2x d-block mb-2 opacity-25"></i>
                            No invoices found. <a href="{{ route('gst-invoices.create') }}">Create your first invoice</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection