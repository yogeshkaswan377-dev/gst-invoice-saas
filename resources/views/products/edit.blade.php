@extends('layouts.app')

@section('title', 'Edit Product - GST Billing Pro')
@section('meta_description', 'Update product details, HSN/SAC codes, GST rates and pricing.')

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
    <a href="{{ route('products.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Edit Product: {{ $product->name }}</h2>
</div>

<form action="{{ route('products.update', $product) }}" method="POST" class="row g-4">
    @csrf
    @method('PUT')

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Product Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Item No *</label>
                        <input type="text" name="item_no" value="{{ old('item_no', $product->item_no) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">HSN/SAC Code</label>
                        <input type="text" name="hsn_sac_code" value="{{ old('hsn_sac_code', $product->hsn_sac_code) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Unit of Measure *</label>
                        <select name="unit" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="pcs" {{ $product->unit == 'pcs' ? 'selected' : '' }}>Pieces (PCS)</option>
                            <option value="kg" {{ $product->unit == 'kg' ? 'selected' : '' }}>Kilogram (KG)</option>
                            <option value="ltr" {{ $product->unit == 'ltr' ? 'selected' : '' }}>Liter (LTR)</option>
                            <option value="mtr" {{ $product->unit == 'mtr' ? 'selected' : '' }}>Meter (MTR)</option>
                            <option value="box" {{ $product->unit == 'box' ? 'selected' : '' }}>Box</option>
                            <option value="hrs" {{ $product->unit == 'hrs' ? 'selected' : '' }}>Hours</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                        <textarea name="description" rows="3" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventory & Stock Card (stock quantity NOT editable here) --}}
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Inventory & Stock Settings</h5>
                <p class="text-muted small"><i class="fas fa-info-circle me-1"></i> Stock quantity cannot be changed here. Use the <strong>Adjust Stock</strong> option on the product list.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Current Stock</label>
                        <input type="text" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px; background:#f8fafc;" value="{{ $product->stock }} {{ $product->stock_unit }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Stock Unit *</label>
                        <select name="stock_unit" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="Mtr" {{ $product->stock_unit == 'Mtr' ? 'selected' : '' }}>Meter (Mtr)</option>
                            <option value="Pcs" {{ $product->stock_unit == 'Pcs' ? 'selected' : '' }}>Pieces (Pcs)</option>
                            <option value="Kg" {{ $product->stock_unit == 'Kg' ? 'selected' : '' }}>Kilogram (Kg)</option>
                            <option value="Roll" {{ $product->stock_unit == 'Roll' ? 'selected' : '' }}>Roll</option>
                            <option value="Box" {{ $product->stock_unit == 'Box' ? 'selected' : '' }}>Box</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Stock Deduction Type *</label>
                        <select name="stock_deduction_type" id="deductionType" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="Meter" {{ $product->stock_deduction_type == 'Meter' ? 'selected' : '' }}>Meter</option>
                            <option value="Piece" {{ $product->stock_deduction_type == 'Piece' ? 'selected' : '' }}>Piece</option>
                            <option value="Kg" {{ $product->stock_deduction_type == 'Kg' ? 'selected' : '' }}>Kg</option>
                            <option value="Roll" {{ $product->stock_deduction_type == 'Roll' ? 'selected' : '' }}>Roll</option>
                            <option value="Box" {{ $product->stock_deduction_type == 'Box' ? 'selected' : '' }}>Box</option>
                            <option value="Custom" {{ $product->stock_deduction_type == 'Custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="consumptionGroup" style="display:{{ $product->stock_deduction_type == 'Piece' ? 'block' : 'none' }};">
                        <label class="form-label fw-semibold" style="font-size:13px;">Consumption per Piece *</label>
                        <input type="number" name="consumption_per_piece" step="0.01" min="0" value="{{ old('consumption_per_piece', $product->consumption_per_piece) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Minimum Stock (Alert)</label>
                        <input type="number" name="minimum_stock" step="0.01" min="0" value="{{ old('minimum_stock', $product->minimum_stock) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Pricing & GST</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Unit Price (₹) *</label>
                    <input type="number" name="unit_price" step="0.01" value="{{ old('unit_price', $product->unit_price) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Selling Price (₹)</label>
                    <input type="number" name="selling_price" step="0.01" value="{{ old('selling_price', $product->selling_price) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    <small class="text-muted">Leave blank to use unit price</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">GST Rate (%) *</label>
                    <select name="gst_rate" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                        @foreach([0,5,12,18,28] as $rate)
                        <option value="{{ $rate }}" {{ $product->gst_rate == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:12px; font-weight:600;">
                    <i class="fas fa-save me-2"></i> Update Product
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('deductionType').addEventListener('change', function() {
        document.getElementById('consumptionGroup').style.display = this.value === 'Piece' ? 'block' : 'none';
    });
</script>
@endpush