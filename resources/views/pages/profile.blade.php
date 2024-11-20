@extends('layouts.app')

@section('content')
    <!-- Toast Notifications Container -->
    <div aria-live="assertive" class="fixed inset-0 flex items-start px-4 py-6 pointer-events-none sm:p-6 z-50">
        <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
            <!-- Success Toast -->
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transform ease-out duration-300 transition"
                    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 3000)"
                    class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ session('success') }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button @click="show = false" type="button"
                                    class="inline-flex text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Toast -->
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transform ease-out duration-300 transition"
                    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 4000)"
                    class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ session('error') }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button @click="show = false" type="button"
                                    class="inline-flex text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Validation Errors Toast -->
            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transform ease-out duration-300 transition"
                    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 5000)"
                    class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">Ada beberapa kesalahan:</p>
                                <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button @click="show = false" type="button"
                                    class="inline-flex text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Profile Settings</h3>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <!-- Photo Section -->
                            <div class="border-b border-gray-200 pb-4">
                                <h4 class="text-md font-medium text-gray-700 mb-3">Profile Photo</h4>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-100">
                                            @if ($user->foto)
                                                <img src="{{ $user->foto }}" alt="Profile photo"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <svg class="h-full w-full text-gray-300" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 14.75c2.67 0 8 1.34 8 4v1.25H4v-1.25c0-2.66 5.33-4 8-4zm0-9.5a4 4 0 110 8 4 4 0 010-8z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <input type="hidden" name="foto" id="foto">
                                        <input type="file" id="foto_input" accept="image/*" class="hidden"
                                            onchange="handlePhotoSelect(this)">
                                        <button type="button" onclick="document.getElementById('foto_input').click()"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Change Photo
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information Section -->
                            <div class="border-b border-gray-200 pb-4">
                                <h4 class="text-md font-medium text-gray-700 mb-3">Basic Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Nama
                                            Lengkap</label>
                                        <input type="text" name="name" id="name"
                                            value="{{ old('name', $user->name) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="email"
                                            class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" id="email"
                                            value="{{ old('email', $user->email) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="nim" class="block text-sm font-medium text-gray-700">NIM</label>
                                        <input type="text" id="nim" value="{{ $user->nim }}" disabled
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                                    </div>

                                    <div>
                                        <label for="no_telepon" class="block text-sm font-medium text-gray-700">Nomor
                                            Telepon</label>
                                        <input type="text" name="no_telepon" id="no_telepon"
                                            value="{{ old('no_telepon', $user->no_telepon) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('no_telepon') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                        @error('no_telepon')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                                    <textarea name="alamat" id="alamat" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('alamat') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                                    @error('alamat')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="pt-4">
                                <h4 class="text-md font-medium text-gray-700 mb-3">Update Password</h4>
                                <p class="text-sm text-gray-500 mb-4">Leave password fields empty if you don't want to
                                    change it</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="current_password"
                                            class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="password" name="current_password" id="current_password"
                                                class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('current_password') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <button type="button" onclick="togglePassword('current_password')"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                    <svg class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg class="h-5 w-5 eye-close hidden" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @error('current_password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">Password
                                            Baru</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="password" name="password" id="password"
                                                class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('password') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <button type="button" onclick="togglePassword('password')"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                    <svg class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg class="h-5 w-5 eye-close hidden" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700">Konfirmasi Password
                                            Baru</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation"
                                                class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <button type="button" onclick="togglePassword('password_confirmation')"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                    <svg class="h-5 w-5 eye-open" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <svg class="h-5 w-5 eye-close hidden" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-5">
                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handlePhotoSelect(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Update preview image
                    const previewImage = input.closest('.flex').querySelector('img');
                    if (previewImage) {
                        previewImage.src = e.target.result;
                    } else {
                        const div = input.closest('.flex').querySelector('.h-24');
                        div.innerHTML =
                            `<img src="${e.target.result}" alt="Profile photo" class="h-full w-full object-cover">`;
                    }

                    // Update hidden input with base64 string
                    document.getElementById('foto').value = e.target.result;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const parentDiv = input.parentElement;
            const eyeOpen = parentDiv.querySelector('.eye-open');
            const eyeClose = parentDiv.querySelector('.eye-close');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClose.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClose.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide toast notifications after delay
            const toasts = document.querySelectorAll('[x-data="{ show: true }"]');
            toasts.forEach(toast => {
                if (toast._x_dataStack) {
                    setTimeout(() => {
                        toast._x_dataStack[0].show = false;
                    }, 3000);
                }
            });

            // Focus first error field if exists
            @if ($errors->any())
                const firstErrorField = document.querySelector('.border-red-300');
                if (firstErrorField) {
                    firstErrorField.focus();
                }
            @endif
        });
    </script>
@endsection
