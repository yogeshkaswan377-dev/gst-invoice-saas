@extends('layouts.app')

@section('title', 'Create GST Invoice - GST Billing Pro')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('gst-invoices.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Create GST Invoice</h2>
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

<form action="{{ route('gst-invoices.store') }}" method="POST" class="row g-4">
    @csrf

    <div class="col-lg-8">
        {{-- Client & Invoice Details --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-user me-2 text-primary"></i>Client & Details</h5>

                {{-- Client Mode Toggle --}}
                <div class="mb-3">
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="client_mode" id="mode_select" value="select" checked onchange="toggleClientMode()">
                            <label class="form-check-label fw-semibold" for="mode_select" style="font-size:13px;">
                                <i class="fas fa-list me-1"></i> Select Existing Client
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="client_mode" id="mode_manual" value="manual" onchange="toggleClientMode()">
                            <label class="form-check-label fw-semibold" for="mode_manual" style="font-size:13px;">
                                <i class="fas fa-pen me-1"></i> Enter Manually
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Select Client (Dropdown) --}}
                <div id="select_client_section">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Client *</label>
                            <select name="client_id" id="client_select" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                                <option value="">Select Client</option>
                                @foreach($clients ?? [] as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} {{ $client->gstin ? '- ' . $client->gstin : '' }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                {{-- Manual Client Entry --}}
                <div id="manual_client_section" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Client Name *</label>
                            <input type="text" name="manual_client_name" value="{{ old('manual_client_name') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., Rahul Sharma">
                            @error('manual_client_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Company Name</label>
                            <input type="text" name="manual_client_company" value="{{ old('manual_client_company') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., ABC Traders">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">GSTIN</label>
                            <input type="text" name="manual_client_gstin" value="{{ old('manual_client_gstin') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px; text-transform:uppercase;" placeholder="22AAAAA0000A1Z5" maxlength="15">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Email</label>
                            <input type="email" name="manual_client_email" value="{{ old('manual_client_email') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="client@email.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                            <input type="text" name="manual_client_phone" value="{{ old('manual_client_phone') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="+91 99999 99999">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:13px;">Address</label>
                            <input type="text" name="manual_client_address" value="{{ old('manual_client_address') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="Street, Area">
                        </div>
                        <div class="col-md-2 position-relative" x-data="stateSearch()">
                            <label class="form-label fw-semibold mb-2" style="font-size:13px;">State *</label>

                            <input type="hidden" name="manual_client_state_name" :value="selectedState?.name || ''">
                            <input type="hidden" name="manual_client_state_code" :value="selectedState?.code || ''">

                            <div class="position-relative">
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="showDropdown=true"
                                    @input="showDropdown=true"
                                    placeholder="Search state"
                                    class="form-control"
                                    style="height:38px; border-radius:10px; padding-left:34px; border:1px solid #dbe4f0; font-size:12px;"
                                    autocomplete="off">
                                <i class="fas fa-location-dot" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:11px;"></i>
                            </div>

                            <div
                                x-show="showDropdown"
                                x-transition
                                @click.outside="showDropdown=false"
                                class="shadow-lg border-0 mt-1"
                                style="position:absolute; width:200px; background:white; border-radius:10px; overflow:hidden; max-height:200px; overflow-y:auto; z-index:999;">

                                <template x-for="state in filteredStates" :key="state.code">
                                    <div @click="selectState(state)"
                                        style="padding:8px 12px; cursor:pointer; border-bottom:1px solid #f1f5f9; font-size:12px;"
                                        onmouseover="this.style.background='#f8fafc'"
                                        onmouseout="this.style.background='white'">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span x-text="state.name"></span>
                                            <span class="badge" style="background:#eff6ff; color:#2563eb; border-radius:8px; font-size:10px;" x-text="state.code"></span>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="filteredStates.length===0" class="text-center p-2 text-muted" style="font-size:11px;">
                                    No state found
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold" style="font-size:13px;">Pincode</label>
                            <input type="text" name="manual_client_pincode" value="{{ old('manual_client_pincode') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="400001">
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> This client will be saved to your client list automatically.</small>
                    </div>
                </div>

                {{-- Invoice Details --}}
                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Invoice Date *</label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        @error('invoice_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Due Date *</label>
                        <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+15 days'))) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        @error('due_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Reference Number</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., PO-12345">
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
                            <option value="fixed">Flat (₹)</option>
                            <option value="percentage">Percent (%)</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Notes</label>
                <textarea name="notes" rows="3" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="Additional notes...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:14px; font-weight:600; font-size:15px;">
            <i class="fas fa-save me-2"></i> Create Invoice
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Client mode toggle
    function toggleClientMode() {
        const modeSelect = document.getElementById('mode_select');
        const selectSection = document.getElementById('select_client_section');
        const manualSection = document.getElementById('manual_client_section');
        const clientSelect = document.getElementById('client_select');

        if (modeSelect.checked) {
            selectSection.style.display = 'block';
            manualSection.style.display = 'none';
            clientSelect.setAttribute('required', 'required');
        } else {
            selectSection.style.display = 'none';
            manualSection.style.display = 'block';
            clientSelect.removeAttribute('required');
        }
    }

    // Items
    let itemIndex = 0;

    function addItem() {
        const container = document.getElementById('items-container');
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.setAttribute('data-index', itemIndex);
        row.innerHTML = `
            <td>
                <input type="text" name="items[${itemIndex}][name]" class="form-control form-control-sm" style="border-radius:8px; border:1px solid #e2e8f0;" placeholder="Item name" required>
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][hsn_sac_code]" class="form-control form-control-sm" style="border-radius:8px; border:1px solid #e2e8f0;" placeholder="HSN/SAC">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm qty-input" style="border-radius:8px; border:1px solid #e2e8f0; width:70px;" value="1" min="1" onchange="calculateTotals()" oninput="calculateTotals()">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm rate-input" style="border-radius:8px; border:1px solid #e2e8f0; width:100px;" placeholder="0.00" step="0.01" onchange="calculateTotals()" oninput="calculateTotals()">
            </td>
            <td>
                <select name="items[${itemIndex}][gst_rate]" class="form-select form-select-sm" style="border-radius:8px; border:1px solid #e2e8f0; width:80px;" onchange="calculateTotals()">
                    <option value="18">18%</option>
                    <option value="5">5%</option>
                    <option value="12">12%</option>
                    <option value="28">28%</option>
                    <option value="0">0%</option>
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

    function removeItem(btn) {
        const row = btn.closest('tr');
        row.remove();
        const rows = document.querySelectorAll('#items-container .item-row');
        rows.forEach((row, index) => {
            row.setAttribute('data-index', index);
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${index}]`));
                }
            });
        });
        itemIndex = rows.length;
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalCgst = 0;
        let totalSgst = 0;
        const rows = document.querySelectorAll('#items-container .item-row');
        rows.forEach(row => {
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
        const grandTotal = subtotal + totalCgst + totalSgst;
        document.getElementById('display-subtotal').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('display-cgst').textContent = '₹' + totalCgst.toFixed(2);
        document.getElementById('display-sgst').textContent = '₹' + totalSgst.toFixed(2);
        document.getElementById('display-total').textContent = '₹' + grandTotal.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function() {
        addItem();
    });
</script>
@endpush