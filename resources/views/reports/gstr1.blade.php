@extends('layouts.app')

@section('title', 'GSTR-1 Report - GST Billing Pro')
@section('meta_description', 'Generate GSTR-1 ready reports with HSN summary, B2B invoices, and export data for GST filing.')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 style="font-size:18px; font-weight:700; margin:0;">GSTR-1 Report</h2>
        <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Generate tax filing ready summaries</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.csv', ['type' => 'gst_invoice', 'from' => request('from', now()->startOfMonth()->format('Y-m-d')), 'to' => request('to', now()->format('Y-m-d'))]) }}"
            class="btn" style="background:#d1fae5; color:#065f46; border-radius:10px; font-weight:600; font-size:13px;">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </a>
        <a href="{{ route('reports.export.excel', ['type' => 'gst_invoice', 'from' => request('from', now()->startOfMonth()->format('Y-m-d')), 'to' => request('to', now()->format('Y-m-d'))]) }}"
            class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; font-weight:600; font-size:13px;">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

{{-- Date Filter --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.gstr1') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:12px;">From Date</label>
                <input type="date" name="from" value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}"
                    class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:12px;">To Date</label>
                <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                    class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; font-weight:600;">
                    <i class="fas fa-sync me-1"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards — Dynamic Data --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color:#3b82f6;">
            <h6 class="text-muted mb-2">Total Invoices</h6>
            <h3 class="mb-0 fw-bold">{{ $summary['total_invoices'] ?? 0 }}</h3>
            <small class="text-muted">B2B + Export</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color:#10b981;">
            <h6 class="text-muted mb-2">Total Taxable Value</h6>
            <h3 class="mb-0 fw-bold">₹{{ number_format($summary['taxable_value'] ?? 0) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color:#f59e0b;">
            <h6 class="text-muted mb-2">Total GST</h6>
            <h3 class="mb-0 fw-bold">₹{{ number_format($summary['total_gst'] ?? 0) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-left-color:#8b5cf6;">
            <h6 class="text-muted mb-2">HSN Codes Used</h6>
            <h3 class="mb-0 fw-bold">{{ $summary['hsn_count'] ?? 0 }}</h3>
        </div>
    </div>
</div>

{{-- HSN Summary Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.05em; background:#f8fafc;">
                    <tr>
                        <th class="ps-4">HSN/SAC</th>
                        <th>Description</th>
                        <th>UQC</th>
                        <th>Total Qty</th>
                        <th>Taxable Value</th>
                        <th>IGST</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hsnSummary ?? [] as $row)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $row->hsn_sac_code }}</td> {{-- 👈 hsn_code → hsn_sac_code --}}
                        <td>{{ $row->description ?? 'N/A' }}</td>
                        <td>{{ $row->total_qty }}</td>
                        <td>₹{{ number_format($row->taxable_value) }}</td>
                        <td>₹{{ number_format($row->igst) }}</td>
                        <td>₹{{ number_format($row->cgst) }}</td>
                        <td>₹{{ number_format($row->sgst) }}</td>
                        <td class="fw-bold">₹{{ number_format($row->total) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No data for selected period</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection