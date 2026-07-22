@extends('layouts.app')

@section('title', 'Company Settings - GST Billing Pro')
@section('meta_description', 'Manage your company profile, GST details, bank information and invoice preferences.')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 style="font-size:18px; font-weight:700; margin:0;">Company Settings</h2>
        <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Manage your business profile & preferences</p>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Company Profile --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-building me-2 text-primary"></i>Company Profile</h5>

                <form action="{{ route('company.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    {{-- Display errors for this form --}}
                    @if ($errors->updateSettings->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->updateSettings->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Company Name *</label>
                            <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                            @error('name', 'updateSettings')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Trade Name</label>
                            <input type="text" name="trade_name" value="{{ old('trade_name', $company->trade_name ?? '') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Email</label>
                            <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Address</label>
                            <textarea name="address" rows="2" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">{{ old('address', $company->address ?? '') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="city" value="{{ old('city', $company->city ?? '') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="City">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="state" value="{{ old('state', $company->state ?? '') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="State">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="pincode" value="{{ old('pincode', $company->pincode ?? '') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="Pincode">
                        </div>

                        {{-- Logo Upload --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Company Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                            @if($company->logo_path)
                            <img src="{{ Storage::url($company->logo_path) }}" class="mt-2 rounded" height="40">
                            @endif
                        </div>

                        {{-- Signature Upload --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Signature</label>
                            <input type="file" name="signature" class="form-control" accept="image/*" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                            @if($company->signature_path)
                            <img src="{{ Storage::url($company->signature_path) }}" class="mt-2 rounded" height="40">
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 24px; font-weight:600;">
                            <i class="fas fa-save me-2"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- GST Settings --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-percent me-2 text-success"></i>GST Configuration</h5>

                <form action="{{ route('company.gst.update') }}" method="POST">
                    @csrf @method('PUT')

                    @if ($errors->updateGst->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->updateGst->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">GSTIN *</label>
                            <input type="text" name="gstin" value="{{ old('gstin', $company->gstin ?? '') }}" class="form-control text-uppercase" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="22AAAAA0000A1Z5" maxlength="15" required>
                            @error('gstin', 'updateGst')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">PAN</label>
                            <input type="text" name="pan" value="{{ old('pan', $company->pan ?? '') }}" class="form-control text-uppercase" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" placeholder="AAAAA0000A" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Default GST Rate (%)</label>
                            <select name="default_gst_rate" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                                @foreach([0,5,12,18,28] as $rate)
                                <option value="{{ $rate }}" {{ old('default_gst_rate', $company->default_gst_rate ?? 18) == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Tax Mode</label>
                            <select name="gst_mode" class="form-select" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                                <option value="exclusive" {{ old('gst_mode', $company->gst_mode ?? 'exclusive') == 'exclusive' ? 'selected' : '' }}>Tax Exclusive</option>
                                <option value="inclusive" {{ old('gst_mode', $company->gst_mode ?? '') == 'inclusive' ? 'selected' : '' }}>Tax Inclusive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 24px; font-weight:600;">
                            <i class="fas fa-save me-2"></i> Save GST Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Invoice Preferences --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-cog me-2 text-info"></i>Invoice Preferences</h5>

                <form action="{{ route('company.preferences.update') }}" method="POST">
                    @csrf @method('PUT')

                    @if ($errors->updatePreferences->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->updatePreferences->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Quote Prefix</label>
                        <input type="text" name="quote_prefix" value="{{ old('quote_prefix', $company->quote_prefix ?? 'QUO') }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Default Payment Terms</label>
                        <select name="payment_terms" class="form-select" style="...">
                            @foreach([
                            '7' => 'Net 7',
                            '15' => 'Net 15',
                            '30' => 'Net 30',
                            '45' => 'Net 45',
                            '0' => 'Due on Receipt'
                            ] as $value => $label)
                            <option value="{{ $value }}" {{ old('payment_terms', $company->default_payment_terms ?? 15) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="show_hsn_sac" value="1" {{ old('show_hsn_sac', $company->show_hsn_sac ?? true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" style="font-size:13px;">Show HSN/SAC in invoices</label>
                    </div>

                    <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:12px; font-weight:600;">
                        <i class="fas fa-save me-2"></i> Save Preferences
                    </button>
                </form>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="card border-danger rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
                <p style="font-size:12px; color:#64748b;">Delete your company and all associated data. This action cannot be undone.</p>
                <button class="btn btn-outline-danger w-100" style="border-radius:10px;" onclick="return confirm('Are you absolutely sure?')">
                    <i class="fas fa-trash me-2"></i> Delete Company
                </button>
            </div>
        </div>
    </div>
</div>
@endsection