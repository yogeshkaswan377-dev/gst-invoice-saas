@extends('layouts.super-admin')

@section('title', 'Company Details')

@section('content')
<div class="mb-6">
    <a href="/super-admin/companies" class="text-sm text-indigo-600 hover:text-indigo-700">
        <i class="fa-solid fa-arrow-left mr-1"></i> Back to Companies
    </a>
</div>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        <div class="h-14 w-14 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
            {{ strtoupper(substr($company->name ?? 'C', 0, 2)) }}
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $company->name ?? 'Company Name' }}</h1>
            <p class="text-sm text-gray-500">{{ $company->email ?? 'No email' }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        @if($company->is_active)
        <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST" onsubmit="return confirm('Suspend this company?');">
            @csrf
            <button type="submit" class="px-4 py-1.5 bg-rose-50 text-rose-700 text-sm font-semibold rounded-lg hover:bg-rose-100 transition">Suspend</button>
        </form>
        @else
        <form action="{{ route('super-admin.companies.approve', $company) }}" method="POST" onsubmit="return confirm('Activate this company?');">
            @csrf
            <button type="submit" class="px-4 py-1.5 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-lg hover:bg-emerald-100 transition">Activate</button>
        </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">GSTIN</span>
        <h3 class="text-lg font-bold text-gray-900 mt-1 font-mono">{{ $company->gstin ?? 'N/A' }}</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Plan</span>
        <h3 class="text-lg font-bold text-indigo-600 mt-1">{{ ucfirst($company->subscription_plan ?? 'Trial') }}</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Users</span>
        <h3 class="text-lg font-bold text-gray-900 mt-1">{{ $company->users_count ?? 1 }}</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/60">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Invoices</span>
        <h3 class="text-lg font-bold text-gray-900 mt-1">{{ $company->invoices_count ?? 0 }}</h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Company Details</h3>
        <div class="space-y-3">
            <div class="flex justify-between text-sm py-2 border-b border-gray-100">
                <span class="text-gray-400">Address</span>
                <span class="font-medium text-gray-900 text-right">{{ $company->address ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between text-sm py-2 border-b border-gray-100">
                <span class="text-gray-400">City</span>
                <span class="font-medium text-gray-900">{{ $company->city ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between text-sm py-2 border-b border-gray-100">
                <span class="text-gray-400">State</span>
                <span class="font-medium text-gray-900">{{ $company->state ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between text-sm py-2">
                <span class="text-gray-400">Joined</span>
                <span class="font-medium text-gray-900">{{ $company->created_at?->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Links</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="/super-admin/companies/{{ $company->id }}/users" class="p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition text-center">
                <i class="fa-solid fa-users text-indigo-600 text-xl mb-1 block"></i>
                <span class="text-sm font-medium text-gray-700">Users</span>
            </a>
            <a href="/super-admin/companies/{{ $company->id }}/invoices" class="p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition text-center">
                <i class="fa-solid fa-file-invoice text-emerald-600 text-xl mb-1 block"></i>
                <span class="text-sm font-medium text-gray-700">Invoices</span>
            </a>
        </div>
    </div>
</div>
@endsection