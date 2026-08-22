@extends('layouts.super-admin')

@section('title', 'Super Admin Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('status') === 'profile-updated')
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4">
        Profile updated successfully!
    </div>
    @endif

    <form action="{{ route('super-admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="superAdminProfileForm">
        @csrf
        @method('PATCH')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6 mb-6">
            <div class="flex items-center gap-4 mb-6">
                <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'SA') . '&background=6366f1&color=fff&size=100' }}"
                    class="h-16 w-16 rounded-xl object-cover">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name ?? 'Super Admin' }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email ?? 'admin@system.io' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Profile Photo</label>
                    <input type="file" name="profile_photo" form="superAdminProfileForm"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-indigo-500"
                        accept="image/*">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                    <i class="fa-solid fa-save mr-2"></i> Save Profile
                </button>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-xl">
                <span class="text-xs font-bold uppercase text-gray-400">Role</span>
                <h4 class="font-semibold text-indigo-600">Super Administrator</h4>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <span class="text-xs font-bold uppercase text-gray-400">Permissions</span>
                <h4 class="font-semibold text-gray-900">Full Platform Access</h4>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
                <span class="text-xs font-bold uppercase text-gray-400">Last Login</span>
                <h4 class="font-semibold text-gray-900">{{ now()->format('d M Y, H:i') }}</h4>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-rose-50 text-rose-700 text-sm font-semibold rounded-xl hover:bg-rose-100 transition">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i> Sign Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection