@extends('layouts.app')

@section('title', 'Clients - GST Billing Pro')
@section('meta_description', 'Manage your clients database. Add, edit, search clients with GSTIN for quick invoicing.')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 style="font-size:18px; font-weight:700; margin:0;">Clients</h2>
        <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Manage your client database with GSTIN</p>
    </div>
    <a href="{{ route('clients.create') }}" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 20px; font-weight:600; text-decoration:none;">
        <i class="fas fa-user-plus me-2"></i> Add Client
    </a>
</div>

{{-- Search + Filters --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('clients.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold" style="font-size:12px;">Search</label>
                <div class="position-relative">
                    <i class="fas fa-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" style="padding-left:38px; border-radius:10px; border:1px solid #e2e8f0;" placeholder="Name, GSTIN, city...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:12px;">State</label>
                <select name="state" class="form-select" style="border-radius:10px; border:1px solid #e2e8f0;" onchange="this.form.submit()">
                    <option value="">All States</option>
                    @foreach($states ?? [] as $code => $name)
                    <option value="{{ $name }}" {{ request('state') == $name ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size:12px;">GST Registered</label>
                <select name="has_gst" class="form-select" style="border-radius:10px; border:1px solid #e2e8f0;" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="1" {{ request('has_gst') == '1' ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ request('has_gst') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn text-white w-100" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; font-weight:600;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Clients Cards --}}
<div class="row g-4">
    @forelse($clients ?? [] as $client)
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 client-card" style="transition:all 0.3s;">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:48px; height:48px; border-radius:14px; background:linear-gradient(135deg, #dbeafe, #ede9fe); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:18px; color:#1e3a8a;">
                        {{ strtoupper(substr($client->name, 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $client->name }}</h6>
                        <small class="text-muted">{{ $client->company_name ?? 'Individual' }}</small>
                    </div>
                </div>

                @if($client->gstin)
                <div class="mb-2">
                    <span class="badge" style="background:#d1fae5; color:#065f46; font-size:11px;">
                        <i class="fas fa-check-circle me-1"></i> GST: {{ $client->gstin }}
                    </span>
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $client->city ?? 'N/A' }}</small>
                    <div>
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-sm" style="background:#dbeafe; color:#1d4ed8; border-radius:8px; font-size:11px;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm" style="background:#fef3c7; color:#92400e; border-radius:8px; font-size:11px;">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-users fa-3x d-block mb-3 opacity-25"></i>
        <h5 class="text-muted">No clients found</h5>
        <a href="{{ route('clients.create') }}" class="btn text-white mt-2" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; padding:8px 20px;">Add Your First Client</a>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $clients->links() ?? '' }}
</div>

<style>
    .client-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endsection