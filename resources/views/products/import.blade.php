@extends('layouts.app')

@section('title', 'Import Products - GST Billing Pro')
@section('meta_description', 'Import products via CSV file.')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('products.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Import Products from CSV</h2>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('products.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold">CSV File *</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                        <small class="text-muted">Max 2MB. Expected columns: item_no, name, description, hsn_sac_code, unit_price, gst_rate, unit, stock, stock_unit, stock_deduction_type, consumption_per_piece, minimum_stock, selling_price.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Import Mode *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="add_new" id="modeAddNew" checked>
                            <label class="form-check-label" for="modeAddNew">
                                <strong>Add New Products</strong> – Insert only new products. Skip duplicates.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="update_existing" id="modeUpdate">
                            <label class="form-check-label" for="modeUpdate">
                                <strong>Update Existing Products</strong> – Update name, price, etc. Stock not changed.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="replace_stock" id="modeReplace">
                            <label class="form-check-label" for="modeReplace">
                                <strong>Replace Stock</strong> – Set stock to exactly the CSV value.
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="add_stock" id="modeAdd">
                            <label class="form-check-label" for="modeAdd">
                                <strong>Add Stock</strong> – Add CSV stock value to existing stock.
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:12px 24px; font-weight:600;">
                        <i class="fas fa-upload me-2"></i> Import
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection