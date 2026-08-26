@extends('layouts.app')

@section('title', 'Edit Proforma - GST Billing Pro')

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

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('proformas.show', $proforma) }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Edit Proforma #{{ $proforma->invoice_number }}</h2>
</div>

<form action="{{ route('proformas.update', $proforma) }}" method="POST" class="row g-4" id="proforma-edit-form">
    @csrf
    @method('PUT')
    <input type="hidden" name="client_mode" value="select">

    <div class="col-lg-8">
        {{-- Client & Details --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Client & Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Client *</label>
                        <select name="client_id" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="">Select Client</option>
                            @foreach($clients ?? [] as $client)
                            <option value="{{ $client->id }}" {{ $proforma->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Invoice Date</label>
                        <input type="date" name="invoice_date" value="{{ $proforma->invoice_date?->format('Y-m-d') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Valid Until</label>
                        <input type="date" name="due_date" value="{{ $proforma->due_date?->format('Y-m-d') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Reference Number</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number', $proforma->reference_number) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Items</h5>
                <div id="proforma-items">
                    @forelse($proforma->items as $index => $item)
                    <div class="row g-2 mb-2 item-row">
                        <div class="col-md-6">
                            <input type="text" name="items[{{ $index }}][name]" value="{{ old("items.$index.name", $item->name) }}" class="form-control" placeholder="Item" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ old("items.$index.quantity", $item->quantity) }}" class="form-control" placeholder="Qty" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", $item->unit_price) }}" class="form-control" placeholder="Rate" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[{{ $index }}][gst_rate]" value="{{ old("items.$index.gst_rate", $item->gst_rate) }}" class="form-control" placeholder="GST %" step="0.01" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm text-danger remove-item" style="background:#fee2e2; border-radius:8px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No items found. Add at least one item.</p>
                    @endforelse
                </div>
                <button type="button" id="add-item-btn" class="btn btn-sm text-white mt-2" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px;">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Additional Details</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Discount Type</label>
                    <select name="discount_type" class="form-select" style="border-radius:10px;">
                        <option value="" {{ old('discount_type', $proforma->discount_type) == '' ? 'selected' : '' }}>None</option>
                        <option value="fixed" {{ old('discount_type', $proforma->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="percentage" {{ old('discount_type', $proforma->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Discount Amount</label>
                    <input type="number" name="discount_amount" step="0.01" value="{{ old('discount_amount', $proforma->discount_amount) }}" class="form-control" style="border-radius:10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Shipping Charges</label>
                    <input type="number" name="shipping_charges" step="0.01" value="{{ old('shipping_charges', $proforma->shipping_charges) }}" class="form-control" style="border-radius:10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Commission</label>
                    <input type="number" name="commission" step="0.01" value="{{ old('commission', $proforma->commission) }}" class="form-control" style="border-radius:10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Payment Terms</label>
                    <input type="text" name="payment_terms" value="{{ old('payment_terms', $proforma->payment_terms ?? 'Net 15') }}" class="form-control" style="border-radius:10px;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Notes</label>
                    <textarea name="notes" rows="2" class="form-control" style="border-radius:10px;">{{ old('notes', $proforma->notes) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Terms & Conditions</label>
                    <textarea name="terms_and_conditions" rows="2" class="form-control" style="border-radius:10px;">{{ old('terms_and_conditions', $proforma->terms_and_conditions) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Status</label>
                    <select name="status" class="form-select" style="border-radius:10px;">
                        <option value="draft" {{ old('status', $proforma->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ old('status', $proforma->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:14px; font-weight:600;">
            <i class="fas fa-save me-2"></i> Update Proforma
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let pIndex = document.querySelectorAll('#proforma-items .item-row').length;

        document.getElementById('add-item-btn').addEventListener('click', function() {
            const container = document.getElementById('proforma-items');
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 item-row';
            row.innerHTML = `
                <div class="col-md-6">
                    <input type="text" name="items[${pIndex}][name]" class="form-control" placeholder="Item" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${pIndex}][quantity]" class="form-control" placeholder="Qty" value="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${pIndex}][unit_price]" class="form-control" placeholder="Rate" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${pIndex}][gst_rate]" class="form-control" placeholder="GST %" step="0.01" value="18" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-sm text-danger remove-item" style="background:#fee2e2; border-radius:8px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(row);
            pIndex++;
        });

        // Remove item handler (event delegation)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                e.target.closest('.item-row').remove();
            }
        });
    });
</script>
@endpush