@extends('layouts.super-admin')

@section('title', 'Audit Trails')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Audit Trails</h1>
        <p class="text-xs text-gray-500 mt-1">Track all system-wide actions and changes.</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200/60 mb-6">
    <form method="GET" action="{{ route('super-admin.audit') }}" class="flex flex-col md:flex-row gap-3 md:items-end">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="User or description..."
                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Action</label>
            <select name="action" class="px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
                <option value="">All Actions</option>
                @foreach(['created', 'updated', 'deleted', 'suspended', 'approved', 'sent', 'exported'] as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/70 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Model</th>
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4">Timestamp</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-600">
                @forelse($logs ?? [] as $log)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        @php
                        $actionColors = [
                        'created' => 'bg-emerald-50 text-emerald-700',
                        'updated' => 'bg-amber-50 text-amber-700',
                        'deleted' => 'bg-rose-50 text-rose-700',
                        'suspended' => 'bg-rose-100 text-rose-800',
                        'approved' => 'bg-emerald-50 text-emerald-700',
                        'sent' => 'bg-indigo-50 text-indigo-700',
                        'exported' => 'bg-purple-50 text-purple-700',
                        ];
                        $color = $actionColors[$log->action] ?? 'bg-gray-50 text-gray-700';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $color }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $log->user->name ?? 'System' }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">
                        {{ class_basename($log->model_type) ?? 'N/A' }} #{{ $log->model_id ?? '' }}
                    </td>
                    <td class="px-6 py-4">{{ $log->description ?? '—' }}</td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-history text-2xl block mb-2 opacity-50"></i>
                        No audit logs found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection