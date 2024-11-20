@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="min-h-screen auth-background flex flex-col items-center justify-center p-4">
        <!-- Card Login -->
        <div class="w-full max-w-md">
            <!-- Card Body dengan Glassmorphism effect -->
            <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-white/20">
                <!-- Logo dan Welcome Text -->
                <div class="text-center">
                    <!-- Contoh Logo SVG - Ganti dengan logo kampus -->
                    <svg class="w-20 h-20 mx-auto mb-6 text-primary" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2L0 9L12 16L22 10.1667V17.5H24V9L12 2ZM12 14.3333L3.53333 9L12 3.66667L20.4667 9L12 14.3333Z" />
                        <path d="M12 16.7333L3 11.3667V13.7667L12 19.1333L21 13.7667V11.3667L12 16.7333Z" />
                        <path d="M12 19.8L3 14.4333V16.8333L12 22.2L21 16.8333V14.4333L12 19.8Z" />
                    </svg>

                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang! 👋</h2>
                    <p class="text-gray-500 text-sm font-medium">Masuk ke Sistem Informasi Akademik</p>
                </div>

                <!-- Form Login with Enhanced Styling -->
                <form method="POST" action="{{ route('login.post') }}" class="mt-8 space-y-6">
                    @csrf

                    <!-- Email Input -->
                    <div class="space-y-1">
                        <label for="email" class="text-sm font-medium text-gray-700 ml-1">Email</label>
                        <div class="relative">
                            <div class="form-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <input type="email" name="email" id="email" class="auth-input"
                                placeholder="nama@email.com" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1">
                        <label for="password" class="text-sm font-medium text-gray-700 ml-1">Password</label>
                        <div class="relative">
                            <div class="form-icon">
                                <i class="bi bi-lock-fill"></i>
                            </div>
                            <input type="password" name="password" id="password" class="auth-input" placeholder="••••••••"
                                required>
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-primary transition-colors">
                                <i class="bi bi-eye-slash text-gray-400" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <span class="ml-2 text-sm text-gray-600"></span>
                        </label>
                        <a href="/forgot-password"
                            class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                            Lupa Password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="auth-button group">
                        <span class="flex items-center justify-center">
                            <i class="bi bi-box-arrow-in-right mr-2 group-hover:translate-x-1 transition-transform"></i>
                            MASUK KE SISTEM
                        </span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white/90 text-gray-500">Quick Links</span>
                    </div>
                </div>

                <!-- Help Center Links with Enhanced Icons -->
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" class="help-link">
                        <i class="bi bi-book"></i>
                        <span>Panduan Pengguna</span>
                    </a>
                    <a href="https://wa.me/6283182192666?text=min%20mau%20joki%20nih%20bisa%20bantu%20nggak%2Cadmin%20ganteng%20deh"
                        class="help-link">
                        <i class="bi bi-headset"></i>
                        <span>Pusat Bantuan</span>
                    </a>
                </div>
            </div>

            <!-- Footer Text -->
            <p class="mt-8 text-center text-sm text-white/80">
                &copy; {{ date('Y') }} Universitas. All rights reserved.
            </p>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            // Toggle input type antara "password" dan "text"
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        }
    </script>
@endpush
