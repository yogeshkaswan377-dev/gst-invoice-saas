@extends('layouts.app')

@section('title', 'Create Company - GST Billing Pro')
@section('meta_description', 'Register a new company with GST details and invoice preferences.')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('company.switch') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Add New Company</h2>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-building me-2 text-primary"></i>Company Details</h5>

                <form action="{{ route('company.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Company Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">GSTIN *</label>
                            <input type="text" name="gstin" value="{{ old('gstin') }}" class="form-control text-uppercase" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="22AAAAA0000A1Z5" maxlength="15" required>
                            @error('gstin')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Address</label>
                            <textarea name="address" rows="2" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="city" value="{{ old('city') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="City">
                        </div>
                        <div class="col-md-4">
                            <select name="state_code" class="form-control" required>
                                <option value="">Select State</option>
                                @foreach($states ?? [] as $code => $name)
                                <option value="{{ $code }}" {{ old('state_code') == $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="pincode" value="{{ old('pincode') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="Pincode">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:12px 30px; font-weight:600;">
                            <i class="fas fa-check-circle me-2"></i> Create Company
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Why Add Company?</h5>
                <ul style="font-size:13px; color:#475569; line-height:2; padding-left:20px;">
                    <li>Manage multiple businesses</li>
                    <li>Separate invoice numbering</li>
                    <li>Different GSTIN per company</li>
                    <li>Isolated client databases</li>
                    <li>Easy switch between companies</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection