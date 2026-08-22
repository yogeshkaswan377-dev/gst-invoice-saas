@extends('layouts.super-admin')

@section('title', 'System Logs')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">System Logs</h1>
        <p class="text-xs text-gray-500 mt-1">Real-time platform event monitoring and diagnostics.</p>
    </div>
    {{-- Level Filter Form --}}
    <form method="GET" action="{{ route('super-admin.logs') }}" class="flex items-center gap-2">
        <select name="level" class="px-3 py-1.5 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
            <option value="">All Levels</option>
            <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
            <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
            <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
            <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>Debug</option>
            <option value="critical" {{ request('level') == 'critical' ? 'selected' : '' }}>Critical</option>
        </select>
        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
            Filter
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/70 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                    <th class="px-6 py-4">Level</th>
                    <th class="px-6 py-4">Message</th>
                    <th class="px-6 py-4">Context</th>
                    <th class="px-6 py-4">Timestamp</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-600">
                @forelse($logs ?? [] as $log)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        @php
                        $levelColors = [
                        'INFO' => 'bg-emerald-50 text-emerald-700',
                        'WARNING' => 'bg-amber-50 text-amber-700',
                        'ERROR' => 'bg-rose-50 text-rose-700',
                        'CRITICAL' => 'bg-rose-100 text-rose-800',
                        'DEBUG' => 'bg-slate-100 text-slate-700',
                        ];
                        $color = $levelColors[$log['level'] ?? 'INFO'] ?? 'bg-gray-50 text-gray-700';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ $color }}">
                            {{ $log['level'] ?? 'INFO' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $log['message'] ?? '' }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ Str::limit($log['context'] ?? '', 80) }}</td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $log['timestamp'] ?? '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-terminal text-2xl block mb-2 opacity-50"></i>
                        No log entries found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection