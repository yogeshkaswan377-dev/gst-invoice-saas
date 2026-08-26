@extends('layouts.app')

@section('title', 'Edit Invoice - GST Billing Pro')

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
        {{-- Items --}}
        <div class="card border-0 shadow-sm rounded-4 mt-4"
            x-data="invoiceItems({{ json_encode($invoice->items->map(function($item) {
         return [
             'name' => $item->name,
             'product_id' => $item->product_id,
             'quantity' => $item->quantity,
             'unit_price' => $item->unit_price,
             'gst_rate' => $item->gst_rate,
             'suggestions' => [],
             'stock_info' => null,
         ];
     })) }})"
            x-init="init()">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Invoice Items</h5>
                    <button type="button" @click="addItem()" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="row g-3 mb-3 border-bottom pb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Product</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" x-model="item.name"
                                    :name="'items['+index+'][name]'"
                                    @input.debounce.500ms="searchProduct(index, $event.target.value)"
                                    placeholder="Search product..." autocomplete="off">
                                <ul class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000; max-height:200px; overflow-y:auto;"
                                    x-show="item.suggestions.length > 0">
                                    <template x-for="suggestion in item.suggestions" :key="suggestion.id">
                                        <li class="list-group-item list-group-item-action" style="cursor:pointer;"
                                            @click="selectProduct(index, suggestion)">
                                            <span x-text="suggestion.name"></span>
                                            <small class="text-muted" x-text="'(' + suggestion.hsn_sac_code + ')'"></small>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            <input type="hidden" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Quantity</label>
                            <input type="number" class="form-control" x-model.number="item.quantity" min="1" step="any"
                                :name="'items['+index+'][quantity]'">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Unit Price</label>
                            <input type="number" class="form-control" x-model.number="item.unit_price" step="0.01"
                                :name="'items['+index+'][unit_price]'">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">GST %</label>
                            <select class="form-select" x-model.number="item.gst_rate" :name="'items['+index+'][gst_rate]'">
                                <option value="0">0%</option>
                                <option value="5">5%</option>
                                <option value="12">12%</option>
                                <option value="18">18%</option>
                                <option value="28">28%</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Total</label>
                            <input type="text" class="form-control" readonly
                                :value="(item.quantity * item.unit_price).toFixed(2)">
                        </div>
                        <!-- Stock info -->
                        <div class="col-12" x-show="item.stock_info">
                            <div class="alert alert-info py-1 px-2 mb-0 small">
                                <span>Stock: <strong x-text="item.stock_info.stock + ' ' + item.stock_info.stock_unit"></strong></span>
                                <span x-show="item.stock_info.stock_deduction_type === 'Piece'">
                                    | 1 Piece = <strong x-text="item.stock_info.consumption_per_piece + ' ' + item.stock_info.stock_unit"></strong>
                                    | Required: <strong x-text="(item.quantity * item.stock_info.consumption_per_piece).toFixed(2) + ' ' + item.stock_info.stock_unit"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-sm text-danger" @click="removeItem(index)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
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
    const PRODUCT_SEARCH_URL = '/products/search';
    const PRODUCT_STOCK_URL_TEMPLATE = '/products/ID_PLACEHOLDER/stock-info';

    function invoiceItems(initialItems = []) {
        return {
            items: initialItems,
            init() {
                if (this.items.length === 0) this.addItem();
                this.$watch('items', () => this.updateTotals(), {
                    deep: true
                });
                window.invoiceAlpine = this;
                this.$nextTick(() => this.attachDiscountListeners());
            },
            attachDiscountListeners() {
                const dt = document.querySelector('select[name="discount_type"]');
                const da = document.querySelector('input[name="discount_amount"]');
                if (dt) dt.addEventListener('change', () => this.updateTotals());
                if (da) da.addEventListener('input', () => this.updateTotals());
            },
            addItem() {
                this.items.push({
                    name: '',
                    product_id: null,
                    quantity: 1,
                    unit_price: 0,
                    gst_rate: 18,
                    suggestions: [],
                    stock_info: null,
                });
            },
            removeItem(index) {
                this.items.splice(index, 1);
            },

            // ── Totals with discount‑adjusted GST ──
            get subtotal() {
                return this.items.reduce((s, i) => s + (i.quantity * i.unit_price), 0);
            },
            get discountAmount() {
                const type = document.querySelector('select[name="discount_type"]')?.value;
                const amt = parseFloat(document.querySelector('input[name="discount_amount"]')?.value) || 0;
                if (type === 'fixed') return amt;
                if (type === 'percentage') return (this.subtotal * amt) / 100;
                return 0;
            },
            get afterDiscount() {
                return Math.max(0, this.subtotal - this.discountAmount);
            },
            get fullTotalGst() {
                return this.items.reduce((s, i) => s + (i.quantity * i.unit_price * i.gst_rate / 100), 0);
            },
            get fullCgst() {
                return this.fullTotalGst / 2;
            },
            get fullSgst() {
                return this.fullTotalGst / 2;
            },
            get ratio() {
                return this.subtotal > 0 ? this.afterDiscount / this.subtotal : 0;
            },
            get totalGst() {
                return this.fullTotalGst * this.ratio;
            },
            get totalCgst() {
                return this.fullCgst * this.ratio;
            },
            get totalSgst() {
                return this.fullSgst * this.ratio;
            },
            get grandTotal() {
                const ship = parseFloat(document.querySelector('input[name="shipping_charges"]')?.value) || 0;
                const comm = parseFloat(document.querySelector('input[name="commission"]')?.value) || 0;
                return this.afterDiscount + this.totalGst + ship + comm;
            },
            updateTotals() {
                document.getElementById('display-subtotal').textContent = '₹' + this.subtotal.toFixed(2);
                document.getElementById('display-cgst').textContent = '₹' + this.totalCgst.toFixed(2);
                document.getElementById('display-sgst').textContent = '₹' + this.totalSgst.toFixed(2);
                document.getElementById('display-total').textContent = '₹' + this.grandTotal.toFixed(2);
            },

            async searchProduct(index, query) {
                if (query.length < 2) {
                    this.items[index].suggestions = [];
                    return;
                }
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const resp = await fetch(PRODUCT_SEARCH_URL + '?q=' + encodeURIComponent(query), {
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await resp.json();
                    this.items[index].suggestions = Array.isArray(data) ? data : [];
                } catch (e) {
                    console.error(e);
                }
            },
            async selectProduct(index, product) {
                this.items[index].name = product.name;
                this.items[index].product_id = product.id;
                this.items[index].unit_price = parseFloat(product.unit_price) || 0;
                this.items[index].gst_rate = parseFloat(product.gst_rate) || 18;
                this.items[index].suggestions = [];
                this.$nextTick(() => {
                    this.items[index].gst_rate = this.items[index].gst_rate;
                });
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const resp = await fetch(PRODUCT_STOCK_URL_TEMPLATE.replace('ID_PLACEHOLDER', product.id), {
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });
                    this.items[index].stock_info = await resp.json();
                } catch (e) {
                    console.error(e);
                }
            }
        };
    }
</script>
@endpush