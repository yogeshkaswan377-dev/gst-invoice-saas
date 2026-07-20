@extends('layouts.app')

@section('title', 'Products & Services - GST Billing Pro')
@section('meta_description', 'Manage your product catalog with HSN/SAC codes. Search, filter, add products for quick invoicing.')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 style="font-size:18px; font-weight:700; margin:0;">Products & Services</h2>
        <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Manage your inventory with HSN/SAC codes</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.import') }}" class="btn btn-outline-secondary " style="border-radius:12px; padding:10px 20px; font-weight:600;" title="Coming Soon">
            <i class="fas fa-upload me-2"></i> Import CSV (Soon)
        </a>
        <a href="{{ route('products.export') }}" class="btn btn-outline-secondary " style="border-radius:12px; padding:10px 20px; font-weight:600;">
            <i class="fas fa-download me-2"></i> Export CSV
        </a>
        <a href="{{ route('products.create') }}" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 20px; font-weight:600; text-decoration:none;">
            <i class="fas fa-plus-circle me-2"></i> Add Product
        </a>
    </div>
</div>

{{-- Search + Filters --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:12px;">Search</label>
                <div class="position-relative">
                    <i class="fas fa-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" style="padding-left:38px; border-radius:10px; border:1px solid #e2e8f0;" placeholder="Search by name, HSN or item no...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:12px;">GST Rate</label>
                <select name="gst_rate" class="form-select" style="border-radius:10px; border:1px solid #e2e8f0;" onchange="this.form.submit()">
                    <option value="">All Rates</option>
                    @foreach([0,5,12,18,28] as $rate)
                    <option value="{{ $rate }}" {{ request('gst_rate') == $rate ? 'selected' : '' }}>{{ $rate }}%</option>
                    @endforeach
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

{{-- Products Grid --}}
<div class="row g-4">
    @forelse($products as $product)
    <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 product-card" style="transition:all 0.3s;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge" style="background:#dbeafe; color:#1d4ed8; font-size:11px;">
                        HSN: {{ $product->hsn_sac_code ?? 'N/A' }}
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-sm text-muted" data-bs-toggle="dropdown" style="border:none;">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end rounded-3 border-0 shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('products.edit', $product) }}"><i class="fas fa-edit me-2 text-primary"></i> Edit</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-current-stock="{{ $product->stock }}"><i class="fas fa-cubes me-2 text-success"></i> Adjust Stock</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.stock-history', $product) }}"><i class="fas fa-history me-2 text-info"></i> Stock History</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <h6 class="fw-bold mb-2">{{ $product->name }}</h6>
                <p class="text-muted" style="font-size:12px; line-height:1.5; flex-grow:1;">
                    {{ $product->description ?? 'No description' }}
                </p>
                <div class="mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Stock</small>
                        <span class="badge bg-light text-dark">{{ $product->stock }} {{ $product->stock_unit }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color:#1e3a8a;">₹{{ number_format($product->unit_price, 2) }}</span>
                        <span class="badge rounded-pill" style="background:#d1fae5; color:#065f46;">{{ $product->gst_rate }}% GST</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-box fa-3x d-block mb-3 opacity-25"></i>
        <h5 class="text-muted">No products found</h5>
        <p class="text-muted" style="font-size:13px;">
            @if(request()->anyFilled(['search', 'type', 'gst_rate']))
            Try different filter criteria
            @else
            Add your first product or service
            @endif
        </p>
        <a href="{{ route('products.create') }}" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:10px; padding:8px 20px;">Add Product</a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>
@endif

{{-- Stock Adjustment Modal --}}
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="adjustStockForm" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Adjust Stock - <span id="modalProductName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Stock: <strong id="modalCurrentStock"></strong></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <select name="adjustment_type" class="form-select" required>
                            <option value="add">Add Stock (+)</option>
                            <option value="deduct">Deduct Stock (-)</option>
                            <option value="set">Set Stock (exact)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" step="0.01" min="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks (optional)</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Reason for adjustment">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endsection

@push('scripts')
<script>
    // Populate modal when Adjust Stock is clicked
    const adjustStockModal = document.getElementById('adjustStockModal');
    adjustStockModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const productId = button.getAttribute('data-product-id');
        const productName = button.getAttribute('data-product-name');
        const currentStock = button.getAttribute('data-current-stock');

        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalCurrentStock').textContent = currentStock;

        // Update form action
        const form = document.getElementById('adjustStockForm');
        form.action = '/products/' + productId + '/adjust-stock'; // adjust route
    });
</script>
@endpush