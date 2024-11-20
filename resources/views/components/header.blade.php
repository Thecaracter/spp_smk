<header class="bg-white shadow-sm border-b">
    <div class="flex justify-between items-center px-8 py-4">
        <!-- Logo -->
        <div class="flex items-center space-x-4">
            <div class="text-primary font-bold text-xl">{{ config('app.name', 'Laravel') }}</div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center space-x-6">
            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-3">
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    @if (Auth::user()->foto)
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->foto }}" alt="Profile">
                    @else
                        <img class="h-8 w-8 rounded-full"
                            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random"
                            alt="Profile">
                    @endif
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50"
                    style="display: none;">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
