@extends('layouts.app')

@section('title', 'Proforma Details - GST Billing Pro')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('proformas.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 style="font-size:18px; font-weight:700; margin:0;">Proforma #{{ $proforma->invoice_number ?? 'N/A' }}</h2>
    </div>
    <a href="{{ route('proformas.pdf', $proforma) }}" class="btn" style="background:#d1fae5; color:#065f46; border-radius:10px; font-weight:600; font-size:13px;">
        <i class="fas fa-file-pdf me-1"></i> PDF
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Items</h5>
                <table class="table table-hover">
                    <thead class="text-muted" style="font-size:11px; background:#f8fafc;">
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>GST</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proforma->items ?? [] as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->gst_rate }}%</td>
                            <td class="fw-bold">₹{{ number_format($item->unit_price * $item->quantity * (1 + $item->gst_rate / 100), 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">No items</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Details</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Client</span>
                    <span class="fw-semibold">{{ $proforma->client->name ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date</span>
                    <span>{{ $proforma->invoice_date?->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Valid Until</span>
                    <span>{{ $proforma->due_date?->format('d M Y') }}</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>₹{{ number_format($proforma->subtotal ?? 0, 2) }}</span>
                </div>

                @if($proforma->discount_amount > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">
                        Discount
                        @if($proforma->discount_type === 'percentage' && $proforma->subtotal > 0)
                        ({{ round(($proforma->discount_amount / $proforma->subtotal) * 100, 2) }}%)
                        @endif
                    </span>
                    <span class="text-danger">-₹{{ number_format($proforma->discount_amount, 2) }}</span>
                </div>
                @endif

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Taxable Amount</span>
                    <span>₹{{ number_format($proforma->taxable_amount ?? 0, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">CGST</span>
                    <span>₹{{ number_format($proforma->cgst_amount ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SGST</span>
                    <span>₹{{ number_format($proforma->sgst_amount ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">IGST</span>
                    <span>₹{{ number_format($proforma->igst_amount ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total GST</span>
                    <span>₹{{ number_format($proforma->total_gst_amount ?? 0, 2) }}</span>
                </div>

                @if($proforma->shipping_charges > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping Charges</span>
                    <span>₹{{ number_format($proforma->shipping_charges, 2) }}</span>
                </div>
                @endif

                @if($proforma->commission > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Commission</span>
                    <span>₹{{ number_format($proforma->commission, 2) }}</span>
                </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Grand Total</span>
                    <span class="fw-bold" style="color:#1e3a8a; font-size:18px;">
                        ₹{{ number_format($proforma->grand_total ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection