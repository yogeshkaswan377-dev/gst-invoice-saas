@extends('layouts.app')

@section('title', 'Create Proforma - GST Billing Pro')

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
    <a href="{{ route('proformas.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Create Proforma Invoice</h2>
</div>

<form action="{{ route('proformas.store') }}" method="POST" class="row g-4">
    @csrf
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Client & Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="hidden" name="client_mode" value="select">
                        <label class="form-label fw-semibold" style="font-size:13px;">Client *</label>
                        <select name="client_id" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            <option value="">Select Client</option>
                            @foreach($clients ?? [] as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Date</label>
                        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Valid Until</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Items</h5>
                <div id="proforma-items">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6"><input type="text" name="items[0][name]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="Item"></div>
                        <div class="col-md-2"><input type="number" name="items[0][quantity]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="Qty" value="1"></div>
                        <div class="col-md-2"><input type="number" name="items[0][unit_price]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="Rate"></div>
                        <div class="col-md-2"><input type="number" name="items[0][gst_rate]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="GST %" value="18"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm text-white mt-2" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px;" onclick="addProformaItem()">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:14px; font-weight:600;">
            <i class="fas fa-save me-2"></i> Save Proforma
        </button>
    </div>
</form>

<script>
    let pIndex = 1;

    function addProformaItem() {
        const container = document.getElementById('proforma-items');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2';
        row.innerHTML = `<div class="col-md-6"><input type="text" name="items[${pIndex}][name]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="Item"></div>
    <div class="col-md-2"><input type="number" name="items[${pIndex}][quantity]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="Qty" value="1"></div>
    <div class="col-md-2"><input type="number" name="items[${pIndex}][unit_price]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="Rate"></div>
    <div class="col-md-2"><input type="number" name="items[${pIndex}][gst_rate]" class="form-control" style="border-radius:10px; border:1px solid #e2e8f0;" placeholder="GST %" value="18"></div>`;
        container.appendChild(row);
        pIndex++;
    }
</script>
@endsection