@extends('layouts.super-admin')

@section('title', 'All Users')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Platform Users</h1>
        <p class="text-xs text-gray-500 mt-1">All registered users across all tenant organizations.</p>
    </div>
</div>

{{-- Search & Filters --}}
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200/60 mb-6">
    <form method="GET" action="{{ route('super-admin.users') }}" class="flex flex-col md:flex-row gap-3 md:items-end">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, company..."
                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Role</label>
            <select name="role" class="px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
                <option value="">All Roles</option>
                <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
                <option value="">All</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
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
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Company</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Joined</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100 text-gray-600">
                @forelse($users ?? [] as $user)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-purple-50 text-purple-600 rounded-xl font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ $user->company->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @php
                        $roleName = $user->roles->pluck('name')->first() ?? 'staff';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700">
                            {{ ucfirst($roleName) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full ring-1 ring-emerald-600/10">
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full ring-1 ring-rose-600/10">
                            Suspended
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $user->created_at?->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('super-admin.users.show', $user) }}" class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg hover:bg-gray-50 transition">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($user->is_active)
                            <form action="{{ route('super-admin.users.suspend', $user) }}" method="POST" onsubmit="return confirm('Suspend this user?');">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-gray-50 transition">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('super-admin.users.approve', $user) }}" method="POST" onsubmit="return confirm('Approve this user?');">
                                @csrf
                                <button type="submit" class="p-2 text-gray-400 hover:text-emerald-600 rounded-lg hover:bg-gray-50 transition">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-users text-2xl block mb-2 opacity-50"></i>
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $users->links() }}
    </div>
</div>
@endsection