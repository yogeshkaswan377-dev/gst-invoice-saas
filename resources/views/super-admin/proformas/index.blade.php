@extends('layouts.super-admin')

@section('title', 'All Proformas')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Proforma Invoices</h1>
        <p class="text-xs text-gray-500 mt-1">Platform-wide proforma invoice registry.</p>
    </div>
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200/60 mb-6">
        <form method="GET" action="{{ route('super-admin.proformas') }}" class="flex gap-3 md:items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Search Proforma</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Proforma #, company, client..."
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                <i class="fa-solid fa-filter mr-1"></i> Apply
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/70 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4">Proforma #</th>
                    <th class="px-6 py-4">Company</th>
                    <th class="px-6 py-4">Client</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-600">
                @forelse($proformas ?? [] as $proforma)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $proforma->invoice_number }}</td>
                    <td class="px-6 py-4">{{ $proforma->company->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $proforma->client->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 font-semibold">₹{{ number_format($proforma->grand_total ?? 0) }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full ring-1 ring-amber-600/10">
                            {{ ucfirst($proforma->status ?? 'Draft') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $proforma->created_at?->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="/super-admin/proformas/{{ $proforma->id }}" class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-gray-50 transition">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">No proformas found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="p-4">
        {{ $proformas->links() }}
    </div>

</div>
@endsection