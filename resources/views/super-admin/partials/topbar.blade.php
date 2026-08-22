<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-30">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = true" class="text-gray-500 lg:hidden focus:outline-none">
            <i class="fa-solid fa-bars-staggered text-xl"></i>
        </button>
        <span class="text-sm font-medium text-gray-500">Root Node</span>
    </div>

    <div class="flex items-center gap-4" x-data="{ profileDropdown: false }">
        <div class="relative">
            <button @click="profileDropdown = !profileDropdown" @click.outside="profileDropdown = false" class="flex items-center gap-2 focus:outline-none">
                @if(auth()->user()->profile_photo_path)
                <img src="{{ auth()->user()->profile_photo_url }}"
                    alt="{{ auth()->user()->name }}"
                    class="h-8 w-8 rounded-lg object-cover shadow-sm">
                @else
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">SA</div>
                @endif
                <span class="text-sm font-semibold text-gray-700 hidden sm:inline">{{ Auth::user()->name ?? 'Super Admin' }}</span>
            </button>
            <div x-show="profileDropdown" class="absolute right-0 mt-2 w-48 rounded-xl bg-white p-1 shadow-lg ring-1 ring-black/5" x-cloak>
                <a href="/super-admin/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">Profile</a>
                <hr class="my-1 border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 rounded-lg">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>