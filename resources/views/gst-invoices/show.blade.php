@extends('layouts.app')

@section('title', 'Invoice Details - GST Billing Pro')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('gst-invoices.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 style="font-size:18px; font-weight:700; margin:0;">Invoice #{{ $invoice->invoice_number ?? 'N/A' }}</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('gst-invoices.pdf', $invoice) }}" class="btn" style="background:#d1fae5; color:#065f46; border-radius:10px; font-weight:600; font-size:13px;">
            <i class="fas fa-file-pdf me-1"></i> PDF
        </a>
        @if($invoice->isEditable())
        <a href="{{ route('gst-invoices.edit', $invoice) }}" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; font-weight:600; font-size:13px;">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-list me-2 text-primary"></i>Items</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="text-muted" style="font-size:11px; background:#f8fafc;">
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>HSN</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>GST</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $items = is_array($invoice->items) ? $invoice->items : json_decode($invoice->items, true) ?? [];
                            @endphp
                            @forelse($items as $index => $item)
                            @php
                            $qty = (int)($item['quantity'] ?? 0);
                            $rate = (float)($item['unit_price'] ?? 0);
                            $gstRate = (float)($item['gst_rate'] ?? 0);
                            $lineTotal = $qty * $rate;
                            $lineTotalWithGst = $lineTotal + ($lineTotal * $gstRate / 100);
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $item['name'] ?? 'N/A' }}</td>
                                <td>{{ $item['hsn_sac_code'] ?? 'N/A' }}</td>
                                <td>{{ $qty }}</td>
                                <td>₹{{ number_format($rate, 2) }}</td>
                                <td>{{ $gstRate }}%</td>
                                <td class="fw-bold">₹{{ number_format($lineTotalWithGst, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background:#f8fafc;">
                            <tr>
                                <td colspan="6" class="text-end fw-semibold">Subtotal:</td>
                                <td class="fw-bold">₹{{ number_format($invoice->subtotal ?? 0, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($invoice->notes)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-sticky-note me-2 text-warning"></i>Notes</h5>
                <p class="mb-0 text-muted">{{ $invoice->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>Details</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Client</span>
                    <span class="fw-semibold">{{ $invoice->client->name ?? 'N/A' }}</span>
                </div>
                @if($invoice->client->gstin ?? false)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">GSTIN</span>
                    <span>{{ $invoice->client->gstin }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date</span>
                    <span>{{ $invoice->invoice_date?->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Due Date</span>
                    <span>{{ $invoice->due_date?->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Place of Supply</span>
                    <span class="badge" style="background:#eff6ff; color:#2563eb;">
                        {{ $invoice->place_of_supply === 'intra_state' ? 'Intra-State' : 'Inter-State' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status</span>
                    @php
                    $statusClass = match($invoice->status) {
                    'paid' => 'badge-paid',
                    'sent', 'viewed' => 'badge-pending',
                    'overdue' => 'badge-overdue',
                    default => 'badge-draft'
                    };
                    @endphp
                    <span class="badge-status {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
                </div>
            </div>
        </div>

        @php
        $isIGST = $invoice->igst_amount > 0;
        $cgst = $invoice->cgst_amount ?? 0;
        $sgst = $invoice->sgst_amount ?? 0;
        $igst = $invoice->igst_amount ?? 0;
        $totalGst = $invoice->total_gst_amount ?? 0;
        $taxable = ($invoice->taxable_amount ?? 0) > 0 ? $invoice->taxable_amount : 1;
        $effectiveRate = round(($totalGst / $taxable) * 100, 2);

        // Determine if we have a detailed breakdown or only a total
        $hasBreakdown = $cgst > 0 || $sgst > 0 || $igst > 0;
        @endphp

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-calculator me-2 text-success"></i>Tax Breakup</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>₹{{ number_format($invoice->subtotal ?? 0, 2) }}</span>
                </div>

                @if($invoice->discount_amount > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Discount</span>
                    <span class="text-danger">-₹{{ number_format($invoice->discount_amount ?? 0, 2) }}</span>
                </div>
                @endif

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Taxable Amount</span>
                    <span>₹{{ number_format($invoice->taxable_amount ?? 0, 2) }}</span>
                </div>

                {{-- New invoices with detailed breakdown --}}
                @if($hasBreakdown)
                @if($isIGST)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">IGST ({{ $effectiveRate }}%)</span>
                    <span>₹{{ number_format($igst, 2) }}</span>
                </div>
                @else
                @if($cgst > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">CGST ({{ round($effectiveRate / 2, 2) }}%)</span>
                    <span>₹{{ number_format($cgst, 2) }}</span>
                </div>
                @endif
                @if($sgst > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SGST ({{ round($effectiveRate / 2, 2) }}%)</span>
                    <span>₹{{ number_format($sgst, 2) }}</span>
                </div>
                @endif
                @endif
                {{-- Fallback for old invoices without breakdown --}}
                @elseif($totalGst > 0)
                @php
                // For old invoices, we don't know CGST/IGST split, so show a generic GST line
                $placeOfSupply = $invoice->place_of_supply;
                $isIntraState = $placeOfSupply === 'intra_state';
                @endphp
                @if($isIntraState)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">CGST + SGST ({{ $effectiveRate }}%)</span>
                    <span>₹{{ number_format($totalGst, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">CGST ({{ round($effectiveRate / 2, 2) }}%)</span>
                    <span>₹{{ number_format($totalGst / 2, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SGST ({{ round($effectiveRate / 2, 2) }}%)</span>
                    <span>₹{{ number_format($totalGst / 2, 2) }}</span>
                </div>
                @else
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">IGST ({{ $effectiveRate }}%)</span>
                    <span>₹{{ number_format($totalGst, 2) }}</span>
                </div>
                @endif
                @endif

                @if($invoice->shipping_charges > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span>₹{{ number_format($invoice->shipping_charges ?? 0, 2) }}</span>
                </div>
                @endif

                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Grand Total</span>
                    <span class="fw-bold" style="color:#1e3a8a; font-size:18px;">₹{{ number_format($invoice->grand_total ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection