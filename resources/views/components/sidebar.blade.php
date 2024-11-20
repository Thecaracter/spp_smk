<aside class="bg-white shadow-sm flex flex-col min-h-screen">
    <!-- Logo Section -->
    <div class="p-4 border-b">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4" :class="{ 'justify-center': !sidebarOpen }">
                <img src="{{ asset('assets/foto/logo.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                <!-- Perbaikan di sini -->
                <div x-show="sidebarOpen">
                    <div class="font-bold text-gray-900">STIKPAR</div>
                    <div class="text-xs text-gray-600">Sistem Informasi Pembayaran</div>
                </div>
            </div>
            <!-- Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-primary">
                <span class="material-icons" x-text="sidebarOpen ? 'menu_open' : 'menu'"></span>
            </button>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-2 flex-1">
        <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
            {{ __('Menu') }}
        </div>

        <a href="{{ route('dashboard') }}"
            class="flex items-center space-x-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}"
            :class="{ 'justify-center': !sidebarOpen }">
            <span class="material-icons text-lg">dashboard</span>
            <span x-show="sidebarOpen">{{ __('Dashboard') }}</span>
        </a>

        @if (Auth::user()->role === 'admin')
            <div class="pt-4">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    {{ __('Admin Menu') }}
                </div>
                <a href="{{ route('users.index') }}"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('users.*') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <span class="material-icons text-lg">people</span>
                    <span x-show="sidebarOpen">{{ __('Users') }}</span>
                </a>
                <a href="{{ route('jenis-pembayaran.index') }}"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('jenis-pembayaran.*') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <span class="material-icons text-lg">credit_score</span>
                    <span x-show="sidebarOpen">{{ __('Jenis Pembayaran') }}</span>
                </a>
                <a href="{{ route('tagihan.index') }}"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('tagihan.*') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <span class="material-icons text-lg">receipt_long</span>
                    <span x-show="sidebarOpen">{{ __('Tagihan') }}</span>
                </a>
                <a href="{{ route('pembayaran.index') }}"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('pembayaran.*') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <span class="material-icons text-lg">payments</span>
                    <span x-show="sidebarOpen">{{ __('Konfirmasi Pembayaran') }}</span>
                </a>
                <a href="{{ route('riwayat.index') }}"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('riwayat.*') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <span class="material-icons text-lg">history</span>
                    <span x-show="sidebarOpen">{{ __('Riwayat Pembayaran') }}</span>
                </a>
            </div>
        @endif

        @if (Auth::user()->role === 'mahasiswa')
            <div class="pt-4">
                <div x-show="sidebarOpen" class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    {{ __('Akademik') }}
                </div>
                <a href="{{ route('user.tagihan.index') }}"
                    class="flex items-center space-x-2 px-4 py-2.5 rounded-lg {{ request()->routeIs('user.tagihan.*') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <span class="material-icons text-lg">receipt_long</span>
                    <span x-show="sidebarOpen">{{ __('Tagihan') }}</span>
                </a>
            </div>
        @endif
    </nav>

    <!-- User Profile Summary - Moved to bottom -->
    <div class="border-t p-4">
        <div class="flex items-center space-x-3" :class="{ 'justify-center': !sidebarOpen }">
            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}"
                alt="Profile">
            <div x-show="sidebarOpen">
                <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-600">{{ Auth::user()->role }}</div>
            </div>
        </div>

        <div x-show="sidebarOpen" class="mt-4 text-xs text-gray-500 text-center">
            © {{ date('Y') }} STIKPAR
        </div>
    </div>
</aside>
