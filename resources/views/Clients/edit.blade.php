@extends('layouts.app')

@section('title', 'Edit Client - ' . $client->name)
@section('meta_description', 'Update client details, GSTIN, address and contact information.')

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
    <h2 style="font-size:18px; font-weight:700; margin:0;">Edit: {{ $client->name }}</h2>
</div>

<form action="{{ route('clients.update', $client) }}" method="POST" class="row g-4">
    @csrf @method('PUT')

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-user-edit me-2 text-primary"></i>Client Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $client->name) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Email</label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">GSTIN</label>
                        <input type="text" name="gstin" value="{{ old('gstin', $client->gstin) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px; text-transform:uppercase;" maxlength="15">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size:13px;">PAN</label>
                        <input type="text" name="pan" value="{{ old('pan', $client->pan) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px; text-transform:uppercase;" maxlength="10">
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
                    <label class="form-label fw-semibold" style="font-size:13px;">Address</label>
                    <textarea name="address_line_1" rows="2" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">{{ old('address_line_1', $client->address_line_1) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">City</label>
                    <input type="text" name="city" value="{{ old('city', $client->city) }}" class="form-control" style="border-radius:12px; border:1px solid #e2e8f0; padding:10px 14px;">
                </div>
                <div class="row g-2">
                    <div class="row g-3">

                        <!-- State -->
                        <div
                            class="col-6 position-relative"
                            x-data="stateSearch()">

                            <label
                                class="form-label fw-semibold mb-2"
                                style="font-size:13px;">
                                State *
                            </label>

                            <!-- Hidden -->
                            <input
                                type="hidden"
                                name="state_name"
                                :value="selectedState?.name || ''">

                            <input
                                type="hidden"
                                name="state_code"
                                :value="selectedState?.code || ''">

                            <!-- Search -->
                            <div class="position-relative">

                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="showDropdown=true"
                                    @input="showDropdown=true"

                                    placeholder="Search state or code"

                                    class="form-control"

                                    style="
                    height:48px;
                    border-radius:14px;
                    padding-left:42px;
                    border:1px solid #dbe4f0;
                    background:#fff;
                "

                                    autocomplete="off"
                                    required>

                                <i
                                    class="fas fa-location-dot"
                                    style="
                    position:absolute;
                    left:14px;
                    top:50%;
                    transform:translateY(-50%);
                    color:#94a3b8;
                ">
                                </i>

                            </div>

                            <!-- Dropdown -->
                            <div
                                x-show="showDropdown"
                                x-transition
                                @click.outside="showDropdown=false"

                                class="shadow-lg border-0 mt-2"

                                style="
                position:absolute;
                width:100%;
                background:white;
                border-radius:14px;
                overflow:hidden;
                max-height:260px;
                overflow-y:auto;
                z-index:999;
            ">

                                <template
                                    x-for="state in filteredStates"
                                    :key="state.code">

                                    <div
                                        @click="selectState(state)"

                                        style="
                        padding:14px 16px;
                        cursor:pointer;
                        border-bottom:1px solid #f1f5f9;
                    "

                                        onmouseover="
                        this.style.background='#f8fafc'
                    "

                                        onmouseout="
                        this.style.background='white'
                    ">

                                        <div
                                            class="d-flex
                        justify-content-between
                        align-items-center">

                                            <span
                                                class="fw-semibold"
                                                x-text="state.name">
                                            </span>

                                            <span
                                                class="badge"
                                                style="
                                background:#eff6ff;
                                color:#2563eb;
                                border-radius:10px;
                            "
                                                x-text="state.code">
                                            </span>

                                        </div>

                                    </div>

                                </template>

                                <div
                                    x-show="filteredStates.length===0"
                                    class="text-center p-3 text-muted">

                                    No state found

                                </div>

                            </div>

                        </div>

                        <!-- Pincode -->
                        <div class="col-6">

                            <label
                                class="form-label fw-semibold mb-2"
                                style="font-size:13px;">

                                Pincode

                            </label>

                            <input
                                type="text"
                                name="pincode"

                                value="{{ old('pincode') }}"

                                class="form-control"

                                placeholder="380001"

                                style="
                height:48px;
                border-radius:14px;
                border:1px solid #dbe4f0;
                padding:0 16px;
            ">

                        </div>

                    </div>
                </div>
            </div>
            {{-- Hidden Required Fields --}}
            <input type="hidden" name="client_type" value="{{ $client->client_type ?? 'business' }}">
            <input type="hidden" name="country" value="{{ $client->country ?? 'India' }}">
            <input type="hidden" name="is_active" value="{{ $client->is_active ?? 1 }}">
        </div>

        <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:14px; font-weight:600;">
            <i class="fas fa-save me-2"></i> Update Client
        </button>
    </div>
</form>

{{-- Delete Card (outside update form) --}}
@can('delete', $client)
<div class="card border-danger rounded-4 mt-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
        <p style="font-size:12px; color:#64748b;">Delete this client permanently.</p>
        <form action="{{ route('clients.destroy', $client) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this client?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger w-100" style="border-radius:10px;">
                <i class="fas fa-trash me-2"></i> Delete Client
            </button>
        </form>
    </div>
</div>
@endcan
</div> {{-- end col-lg-4 --}}
</div> {{-- end row --}}

@endsection