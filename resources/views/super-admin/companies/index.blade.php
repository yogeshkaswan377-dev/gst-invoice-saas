@extends('layouts.super-admin')

@section('title', 'All Companies')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Registered Companies</h1>
        <p class="text-xs text-gray-500 mt-1">Manage tenant organizations, metrics allocation, and accounts.</p>
    </div>
    {{-- REMOVED Add Company button --}}
</div>

{{-- Search Form --}}
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200/60 mb-6">
    <form method="GET" action="{{ route('super-admin.companies') }}" class="flex flex-col md:flex-row gap-3 md:items-end">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search Company</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, GSTIN, city..."
                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
            <i class="fa-solid fa-filter mr-1"></i> Apply
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/70 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4">Company</th>
                    <th class="px-6 py-4">Owner</th>
                    <th class="px-6 py-4">Plan</th>
                    <th class="px-6 py-4">Users</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Created</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-600">
                @forelse($companies ?? [] as $company)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-xl font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($company->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $company->name }}</h4>
                                <p class="text-xs text-gray-400">{{ $company->email ?? 'No email' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ $company->owner->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700">
                            {{ ucfirst($company->subscription_plan ?? 'Trial') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $company->users_count ?? 1 }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 text-xs font-medium {{ $company->is_active ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }} px-2.5 py-0.5 rounded-full ring-1 {{ $company->is_active ? 'ring-emerald-600/10' : 'ring-rose-600/10' }}">
                            {{ $company->is_active ? 'Active' : 'Suspended' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $company->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex items-center gap-1">
                            <a href="{{ route('super-admin.companies.show', $company) }}" class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-gray-50 transition" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('super-admin.companies.users', $company) }}" class="p-2 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-gray-50 transition" title="Users">
                                <i class="fa-solid fa-users"></i>
                            </a>
                            <a href="{{ route('super-admin.companies.invoices', $company) }}" class="p-2 text-gray-400 hover:text-emerald-600 rounded-lg hover:bg-gray-50 transition" title="Invoices">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                            @if($company->is_active)
                            <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST" onsubmit="return confirm('Suspend this company?');">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-gray-50 transition" title="Suspend">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('super-admin.companies.approve', $company) }}" method="POST" onsubmit="return confirm('Activate this company?');">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-emerald-600 rounded-lg hover:bg-gray-50 transition" title="Activate">
                                    <i class="fa-solid fa-circle-check"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-inbox text-2xl block mb-2 opacity-50"></i>
                        No companies found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $companies->links() }}
    </div>
</div>
@endsection