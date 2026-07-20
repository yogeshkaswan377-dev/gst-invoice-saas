@extends('layouts.app')

@section('title', 'Edit Invoice - GST Billing Pro')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('gst-invoices.show', $invoice) }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Edit Invoice #{{ $invoice->invoice_number }}</h2>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
<div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Please fix the following errors:</strong>
    </div>
    <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('gst-invoices.update', $invoice) }}" method="POST" class="row g-4">
    @csrf @method('PUT')

    <div class="col-lg-8">
        {{-- Client & Invoice Details --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-user me-2 text-primary"></i>Client & Details</h5>

                {{-- Client Selection --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Client *</label>
                        <select name="client_id" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="">Select Client</option>
                            @foreach($clients ?? [] as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} {{ $client->gstin ? '- ' . $client->gstin : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                {{-- Invoice Details --}}
                <hr class="my-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Invoice Date *</label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d')) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        @error('invoice_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Due Date *</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        @error('due_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Reference Number</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number', $invoice->reference_number) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., PO-12345">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Status</label>
                        <select name="status" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                            <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ old('status', $invoice->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i>Line Items</h5>
                    <button type="button" class="btn btn-sm text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px;" onclick="addItem()">
                        <i class="fas fa-plus me-1"></i> Add Item
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless" id="items-table">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th style="font-size:11px; font-weight:700; color:#64748b;">Item *</th>
                                <th style="font-size:11px; font-weight:700; color:#64748b;">HSN/SAC</th>
                                <th style="font-size:11px; font-weight:700; color:#64748b;">Qty *</th>
                                <th style="font-size:11px; font-weight:700; color:#64748b;">Rate (₹) *</th>
                                <th style="font-size:11px; font-weight:700; color:#64748b;">GST %</th>
                                <th style="font-size:11px; font-weight:700; color:#64748b;">Amount</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                        </tbody>
                    </table>
                </div>

                @error('items') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Totals --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-calculator me-2 text-success"></i>Invoice Totals</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold" id="display-subtotal">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">CGST</span>
                    <span class="fw-semibold" id="display-cgst">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">SGST</span>
                    <span class="fw-semibold" id="display-sgst">₹0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Grand Total</span>
                    <span class="fw-bold" id="display-total" style="color:#1e3a8a; font-size:18px;">₹0.00</span>
                </div>
            </div>
        </div>

        {{-- Discount --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-tag me-2 text-warning"></i>Discount</h5>
                <div class="row g-2">
                    <div class="col-md-5">
                        <select name="discount_type" class="form-select" style="border-radius:10px; border:1px solid #e2e8f0; font-size:13px;">
                            <option value="fixed" {{ old('discount_type', $invoice->discount_type) == 'fixed' ? 'selected' : '' }}>Flat (₹)</option>
                            <option value="percentage" {{ old('discount_type', $invoice->discount_type) == 'percentage' ? 'selected' : '' }}>Percent (%)</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="number" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount) }}" step="0.01" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Charges --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-truck me-2 text-info"></i>Additional Charges</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Shipping Charges (₹)</label>
                    <input type="number" name="shipping_charges" value="{{ old('shipping_charges', $invoice->shipping_charges) }}" step="0.01" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="0.00">
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Notes</label>
                <textarea name="notes" rows="3" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="Additional notes...">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:14px; font-weight:600; font-size:15px;">
            <i class="fas fa-save me-2"></i> Update Invoice
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let itemIndex = 0;
    const existingItems = {!! $invoice->items->map(function($item) {
        return [
            'name' => $item->name,
            'hsn_sac_code' => $item->hsn_sac_code,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'gst_rate' => $item->gst_rate,
        ];
    })->toJson() !!};

    function addItem(data = null) {
        const container = document.getElementById('items-container');
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.setAttribute('data-index', itemIndex);

        let name = '', hsn = '', qty = 1, rate = '', gst = 18;

        if (data) {
            name = data.name || '';
            hsn = data.hsn_sac_code || '';
            qty = data.quantity || 1;
            rate = data.unit_price || '';
            gst = data.gst_rate || 18;
        }

        row.innerHTML = `
            <td>
                <input type="text" name="items[${itemIndex}][name]" value="${escapeHtml(name)}" class="form-control form-control-sm" style="border-radius:8px; border:1px solid #e2e8f0;" placeholder="Item name" required>
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][hsn_sac_code]" value="${escapeHtml(hsn)}" class="form-control form-control-sm" style="border-radius:8px; border:1px solid #e2e8f0;" placeholder="HSN/SAC">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" value="${qty}" class="form-control form-control-sm qty-input" style="border-radius:8px; border:1px solid #e2e8f0; width:70px;" min="1" onchange="calculateTotals()" oninput="calculateTotals()">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_price]" value="${rate}" class="form-control form-control-sm rate-input" style="border-radius:8px; border:1px solid #e2e8f0; width:100px;" placeholder="0.00" step="0.01" onchange="calculateTotals()" oninput="calculateTotals()">
            </td>
            <td>
                <select name="items[${itemIndex}][gst_rate]" class="form-select form-select-sm" style="border-radius:8px; border:1px solid #e2e8f0; width:80px;" onchange="calculateTotals()">
                    <option value="0" ${gst == 0 ? 'selected' : ''}>0%</option>
                    <option value="5" ${gst == 5 ? 'selected' : ''}>5%</option>
                    <option value="12" ${gst == 12 ? 'selected' : ''}>12%</option>
                    <option value="18" ${gst == 18 ? 'selected' : ''}>18%</option>
                    <option value="28" ${gst == 28 ? 'selected' : ''}>28%</option>
                </select>
            </td>
            <td class="fw-semibold item-amount" style="font-size:13px;">₹0.00</td>
            <td>
                <button type="button" class="btn btn-sm text-danger" onclick="removeItem(this)" style="background:none; padding:2px 6px;">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        container.appendChild(row);
        itemIndex++;
        calculateTotals();
    }

    function escapeHtml(text) {
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function removeItem(btn) {
        const row = btn.closest('tr');
        row.remove();
        const rows = document.querySelectorAll('#items-container .item-row');
        rows.forEach((row, index) => {
            row.setAttribute('data-index', index);
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + index + ']'));
                }
            });
        });
        itemIndex = rows.length;
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0, totalCgst = 0, totalSgst = 0;
        document.querySelectorAll('#items-container .item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
            const rate = parseFloat(row.querySelector('.rate-input')?.value) || 0;
            const gstRate = parseFloat(row.querySelector('select[name*="[gst_rate]"]')?.value) || 0;
            const amount = qty * rate;
            const amountCell = row.querySelector('.item-amount');
            if (amountCell) amountCell.textContent = '₹' + amount.toFixed(2);
            subtotal += amount;
            const gstAmount = amount * (gstRate / 100);
            totalCgst += gstAmount / 2;
            totalSgst += gstAmount / 2;
        });
        document.getElementById('display-subtotal').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('display-cgst').textContent = '₹' + totalCgst.toFixed(2);
        document.getElementById('display-sgst').textContent = '₹' + totalSgst.toFixed(2);
        document.getElementById('display-total').textContent = '₹' + (subtotal + totalCgst + totalSgst).toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (existingItems && existingItems.length > 0) {
            existingItems.forEach(item => addItem(item));
        } else {
            addItem();
        }
    });
</script>
@endpush
