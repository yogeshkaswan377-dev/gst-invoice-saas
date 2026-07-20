@extends('layouts.app')

@section('title', 'Stock History - ' . $product->name)
@section('meta_description', 'View all stock adjustments for ' . $product->name)

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('products.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Stock History: {{ $product->name }}</h2>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <small class="text-muted">Item No</small>
                <div class="fw-semibold">{{ $product->item_no ?? 'N/A' }}</div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Current Stock</small>
                <div class="fw-semibold">{{ $product->stock }} {{ $product->stock_unit }}</div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Deduction Type</small>
                <div class="fw-semibold">{{ $product->stock_deduction_type }}</div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Min. Stock Alert</small>
                <div class="fw-semibold">{{ $product->minimum_stock }} {{ $product->stock_unit }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Action</th>
                        <th>Type</th>
                        <th>Qty / Consumed</th>
                        <th>Previous</th>
                        <th>Current</th>
                        <th>Remarks</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    <tr>
                        <td class="ps-4">{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y, h:i A') }}</td>
                        <td>
                            @if(str_contains($history->action, 'manual'))
                            <span class="badge bg-warning text-dark">Manual {{ ucfirst(str_replace('manual_', '', $history->action)) }}</span>
                            @elseif(str_contains($history->action, 'invoice'))
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $history->action)) }}</span>
                            @elseif(str_contains($history->action, 'csv'))
                            <span class="badge bg-secondary">CSV Import</span>
                            @else
                            <span class="badge bg-primary">{{ $history->action }}</span>
                            @endif
                        </td>
                        <td>{{ $history->stock_deduction_type ?? '-' }}</td>
                        <td>
                            @if($history->invoice_quantity)
                            {{ $history->invoice_quantity }} pcs → {{ $history->consumed_stock }} {{ $product->stock_unit }}
                            @else
                            {{ $history->consumed_stock }} {{ $product->stock_unit }}
                            @endif
                        </td>
                        <td>{{ $history->previous_stock }}</td>
                        <td>{{ $history->current_stock }}</td>
                        <td>{{ $history->remarks ?? '-' }}</td>
                        <td>
                            @if($history->reference_type == 'manual')
                            Manual
                            @elseif($history->reference_type == 'invoice')
                            <a href="{{ route('invoices.show', $history->reference_id) }}" class="text-decoration-none">INV-{{ $history->reference_id }}</a>
                            @else
                            {{ $history->reference_type ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No stock history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection