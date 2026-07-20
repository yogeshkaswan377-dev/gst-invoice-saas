@extends('layouts.app')

@section('title', 'Add New Product - GST Billing Pro')
@section('meta_description', 'Add new product or service with HSN/SAC codes and GST rates to your catalog.')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('products.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Add New Product</h2>
</div>

<form action="{{ route('products.store') }}" method="POST" class="row g-4">
    @csrf

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Product Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Item No *</label>
                        <input type="text" name="item_no" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., 10001" required>
                        <small class="text-muted">Unique per company</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Product Name *</label>
                        <input type="text" name="name" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., Consulting Service, Steel Rods" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">HSN/SAC Code</label>
                        <input type="text" name="hsn_sac_code" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., 998313, 7214">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Unit of Measure *</label>
                        <select name="unit" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="pcs">Pieces (PCS)</option>
                            <option value="kg">Kilogram (KG)</option>
                            <option value="ltr">Liter (LTR)</option>
                            <option value="mtr">Meter (MTR)</option>
                            <option value="box">Box</option>
                            <option value="hrs">Hours</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                        <textarea name="description" rows="3" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="Brief product description..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventory & Stock Card --}}
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Inventory & Stock Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Initial Stock *</label>
                        <input type="number" name="stock" step="0.01" min="0" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Stock Unit *</label>
                        <select name="stock_unit" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="Mtr">Meter (Mtr)</option>
                            <option value="Pcs">Pieces (Pcs)</option>
                            <option value="Kg">Kilogram (Kg)</option>
                            <option value="Roll">Roll</option>
                            <option value="Box">Box</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Stock Deduction Type *</label>
                        <select name="stock_deduction_type" class="form-select" id="deductionType" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="Meter">Meter</option>
                            <option value="Piece">Piece</option>
                            <option value="Kg">Kg</option>
                            <option value="Roll">Roll</option>
                            <option value="Box">Box</option>
                            <option value="Custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="consumptionGroup" style="display:none;">
                        <label class="form-label fw-semibold" style="font-size:13px;">Consumption per Piece *</label>
                        <input type="number" name="consumption_per_piece" step="0.01" min="0" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="e.g., 1.60 (for Piece type)">
                        <small class="text-muted">Required when deduction type is Piece</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Minimum Stock (Alert)</label>
                        <input type="number" name="minimum_stock" step="0.01" min="0" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" value="0">
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
                    <input type="number" name="unit_price" step="0.01" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="0.00" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Selling Price (₹)</label>
                    <input type="number" name="selling_price" step="0.01" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="If different from unit price">
                    <small class="text-muted">Leave blank to use unit price</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">GST Rate (%) *</label>
                    <select name="gst_rate" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        <option value="0">0% (Nil Rated)</option>
                        <option value="5">5%</option>
                        <option value="12">12%</option>
                        <option value="18" selected>18%</option>
                        <option value="28">28%</option>
                    </select>
                </div>

                <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:12px; font-weight:600;">
                    <i class="fas fa-save me-2"></i> Save Product
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
    // Trigger on load if old value is Piece (for edit validation errors)
    window.addEventListener('DOMContentLoaded', function() {
        const dType = document.getElementById('deductionType');
        if (dType.value === 'Piece') {
            document.getElementById('consumptionGroup').style.display = 'block';
        }
    });
</script>
@endpush