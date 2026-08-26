@extends('layouts.app')

@section('title', 'Add New Client - GST Billing Pro')

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
    <a href="{{ route('clients.index') }}" class="btn btn-sm" style="background:#f1f5f9; border-radius:10px; color:#64748b;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 style="font-size:18px; font-weight:700; margin:0;">Add New Client</h2>
</div>

<form action="{{ route('clients.store') }}" method="POST" class="row g-4">
    @csrf

    <div class="col-lg-8">
        {{-- Client Type Selection --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-tag me-2 text-primary"></i>Client Type</h5>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="client_type" id="type_business" value="business" {{ old('client_type') == 'individual' ? '' : 'checked' }}>
                        <label class="form-check-label fw-semibold" for="type_business">
                            <i class="fas fa-building me-1"></i> Business
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="client_type" id="type_individual" value="individual" {{ old('client_type') == 'individual' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="type_individual">
                            <i class="fas fa-user me-1"></i> Individual
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Client Information --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-user me-2 text-primary"></i>Client Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Full Name *</label>
                        <input type="text" name="name" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" value="{{ old('name') }}" required>
                    </div>

                    {{-- Company Name (Business only) --}}
                    <div class="col-md-6" id="company_name_field">
                        <label class="form-label fw-semibold" style="font-size:13px;">Company Name <span class="text-danger" id="company_required">*</span></label>
                        <input type="text" name="company_name" id="company_name_input" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" value="{{ old('company_name') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Email</label>
                        <input type="email" name="email" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                        <input type="text" name="phone" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" value="{{ old('phone') }}">
                    </div>

                    {{-- GSTIN (Business only) --}}
                    <div class="col-md-6" id="gstin_field">
                        <label class="form-label fw-semibold" style="font-size:13px;">GSTIN <span class="text-danger" id="gstin_required">*</span></label>
                        <input type="text" name="gstin" id="gstin_input" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px; text-transform:uppercase;" value="{{ old('gstin') }}" placeholder="22AAAAA0000A1Z5" maxlength="15">
                    </div>

                    {{-- PAN --}}
                    <div class="col-md-6" id="pan_field">
                        <label class="form-label fw-semibold" style="font-size:13px;">PAN</label>
                        <input type="text" name="pan" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px; text-transform:uppercase;" value="{{ old('pan') }}" placeholder="AAAAA0000A" maxlength="10">
                    </div>

                    {{-- Individual Notice --}}
                    <div class="col-12 d-none" id="individual_notice">
                        <div class="alert border-0 rounded-3 mb-0" style="background:#f0fdf4; color:#166534;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>B2C Client:</strong> GSTIN not required. Invoices will be marked as B2C.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Address</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Address Line 1</label>
                    <textarea name="address_line_1" rows="2" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">{{ old('address_line_1') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">City</label>
                    <input type="text" name="city" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" value="{{ old('city') }}">
                </div>

                <div class="row g-3">
                    {{-- State Search (Alpine.js - same as edit form) --}}
                    <div class="col-6 position-relative" x-data="stateSearch()">
                        <label class="form-label fw-semibold mb-2" style="font-size:13px;">State *</label>

                        <input type="hidden" name="state_name" :value="selectedState?.name || ''">
                        <input type="hidden" name="state_code" :value="selectedState?.code || ''">

                        <div class="position-relative">
                            <input
                                type="text"
                                x-model="search"
                                @focus="showDropdown=true"
                                @input="showDropdown=true"
                                placeholder="Search state or code"
                                class="form-control"
                                style="height:48px; border-radius:14px; padding-left:42px; border:1px solid #dbe4f0; background:#fff;"
                                autocomplete="off"
                                required>
                            <i class="fas fa-location-dot" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
                        </div>

                        <div
                            x-show="showDropdown"
                            x-transition
                            @click.outside="showDropdown=false"
                            class="shadow-lg border-0 mt-2"
                            style="position:absolute; width:100%; background:white; border-radius:14px; overflow:hidden; max-height:260px; overflow-y:auto; z-index:999;">

                            <template x-for="state in filteredStates" :key="state.code">
                                <div @click="selectState(state)"
                                    style="padding:14px 16px; cursor:pointer; border-bottom:1px solid #f1f5f9;"
                                    onmouseover="this.style.background='#f8fafc'"
                                    onmouseout="this.style.background='white'">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold" x-text="state.name"></span>
                                        <span class="badge" style="background:#eff6ff; color:#2563eb; border-radius:10px;" x-text="state.code"></span>
                                    </div>
                                </div>
                            </template>

                            <div x-show="filteredStates.length===0" class="text-center p-3 text-muted">
                                No state found
                            </div>
                        </div>
                    </div>

                    {{-- Pincode --}}
                    <div class="col-6">
                        <label class="form-label fw-semibold mb-2" style="font-size:13px;">Pincode</label>
                        <input
                            type="text"
                            name="pincode"
                            value="{{ old('pincode') }}"
                            class="form-control"
                            placeholder="380001"
                            style="height:48px; border-radius:14px; border:1px solid #dbe4f0; padding:0 16px;">
                    </div>
                </div>
            </div>

            {{-- Hidden Fields --}}
            <input type="hidden" name="country" value="India">
            <input type="hidden" name="is_active" value="1">
        </div>

        <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:14px; font-weight:600; font-size:15px;">
            <i class="fas fa-save me-2"></i> Save Client
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Toggle fields based on client type
    document.addEventListener('DOMContentLoaded', function() {
        const businessRadio = document.getElementById('type_business');
        const individualRadio = document.getElementById('type_individual');
        const companyField = document.getElementById('company_name_field');
        const companyInput = document.getElementById('company_name_input');
        const companyRequired = document.getElementById('company_required');
        const gstinField = document.getElementById('gstin_field');
        const gstinInput = document.getElementById('gstin_input');
        const gstinRequired = document.getElementById('gstin_required');
        const panField = document.getElementById('pan_field');
        const individualNotice = document.getElementById('individual_notice');

        function toggleFields() {
            if (individualRadio.checked) {
                // Individual
                companyField.classList.add('d-none');
                companyInput.removeAttribute('required');
                companyRequired.style.display = 'none';
                gstinField.classList.add('d-none');
                gstinInput.removeAttribute('required');
                gstinRequired.style.display = 'none';
                panField.classList.add('d-none');
                individualNotice.classList.remove('d-none');
            } else {
                // Business
                companyField.classList.remove('d-none');
                companyInput.setAttribute('required', 'required');
                companyRequired.style.display = 'inline';
                gstinField.classList.remove('d-none');
                gstinInput.setAttribute('required', 'required');
                gstinRequired.style.display = 'inline';
                panField.classList.remove('d-none');
                individualNotice.classList.add('d-none');
            }
        }

        businessRadio.addEventListener('change', toggleFields);
        individualRadio.addEventListener('change', toggleFields);

        // Run on page load
        toggleFields();
    });
</script>
@endpush