    @extends('layouts.app')

    @section('title', 'Proformas - GST Billing Pro')
    @section('meta_description', 'Manage proforma invoices. Convert to tax invoices with one click.')

    @section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 style="font-size:18px; font-weight:700; margin:0;">Proformas</h2>
            <p style="color:#64748b; font-size:12px; margin:4px 0 0;">Draft estimates & proforma invoices</p>
        </div>
        <a href="{{ route('proformas.create') }}" class="btn text-white" style="background:linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius:12px; padding:10px 20px; font-weight:600; text-decoration:none;">
            <i class="fas fa-plus-circle me-2"></i> Create Proforma
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:0.05em; background:#f8fafc;">
                        <tr>
                            <th class="ps-4">Proforma #</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proformas ?? [] as $proforma)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $proforma->invoice_number }}</td>
                            <td>{{ $proforma->client->name ?? 'N/A' }}</td>
                            <td>{{ $proforma->invoice_date?->format('d M Y') }}</td>
                            <td class="fw-bold">₹{{ number_format($proforma->grand_total ?? 0) }}</td>
                            <td>
                                <a href="{{ route('proformas.show', $proforma) }}" class="btn btn-sm" style="background:#dbeafe; color:#1d4ed8; border-radius:8px; font-size:11px;">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No proformas found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endsection 