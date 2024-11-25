@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="min-h-screen auth-background flex flex-col items-center justify-center p-4 relative overflow-hidden">
        <!-- Animated background circles -->
        <div class="absolute inset-0 overflow-hidden z-0">
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-primary/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-primary/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Card Login -->
        <div class="w-full max-w-md z-10">
            <!-- Card Body dengan Enhanced Glassmorphism -->
            <div
                class="bg-white/95 backdrop-blur-2xl rounded-3xl shadow-xl p-8 border border-white/30 transform hover:scale-[1.01] transition-all duration-300">
                <!-- Logo dan Welcome Text dengan Animation -->
                <div class="text-center transform hover:scale-105 transition-transform duration-300">
                    <!-- Logo SVG dengan Animation -->
                    <svg class="w-24 h-24 mx-auto mb-6 text-primary animate-float" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2L0 9L12 16L22 10.1667V17.5H24V9L12 2ZM12 14.3333L3.53333 9L12 3.66667L20.4667 9L12 14.3333Z" />
                        <path d="M12 16.7333L3 11.3667V13.7667L12 19.1333L21 13.7667V11.3667L12 16.7333Z" />
                        <path d="M12 19.8L3 14.4333V16.8333L12 22.2L21 16.8333V14.4333L12 19.8Z" />
                    </svg>

                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang! 👋</h2>
                    <p class="text-gray-500 text-sm font-medium">Masuk ke Sistem Informasi Akademik</p>
                </div>

                <!-- Enhanced Form Login -->
                <form method="POST" action="{{ route('login.post') }}" class="mt-8 space-y-6">
                    @csrf

                    <!-- Email Input dengan hover effect -->
                    <div class="space-y-2 transform hover:scale-[1.02] transition-all duration-300">
                        <label for="email" class="text-sm font-semibold text-gray-700 ml-1 flex items-center gap-2">
                            <i class="bi bi-envelope text-primary"></i>
                            Email
                        </label>
                        <div class="relative group">
                            <div class="form-icon group-hover:text-primary transition-colors">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <input type="email" name="email" id="email"
                                class="auth-input focus:ring-2 focus:ring-primary/50" placeholder="nama@email.com"
                                value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input dengan hover effect -->
                    <div class="space-y-2 transform hover:scale-[1.02] transition-all duration-300">
                        <label for="password" class="text-sm font-semibold text-gray-700 ml-1 flex items-center gap-2">
                            <i class="bi bi-shield-lock text-primary"></i>
                            Password
                        </label>
                        <div class="relative group">
                            <div class="form-icon group-hover:text-primary transition-colors">
                                <i class="bi bi-lock-fill"></i>
                            </div>
                            <input type="password" name="password" id="password"
                                class="auth-input focus:ring-2 focus:ring-primary/50" placeholder="••••••••" required>
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors">
                                <i class="bi bi-eye-slash text-lg" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Enhanced Remember Me & Forgot Password -->
                    <div class="flex items-center justify-end">
                        <a href="/forgot-password"
                            class="text-sm font-medium text-primary hover:text-primary-dark transition-colors hover:underline">
                            Lupa Password?
                        </a>
                    </div>

                    <!-- Enhanced Login Button -->
                    <button type="submit"
                        class="auth-button group relative overflow-hidden transform hover:scale-105 transition-all duration-300">
                        <span class="flex items-center justify-center relative z-10">
                            <i class="bi bi-box-arrow-in-right mr-2 group-hover:translate-x-1 transition-transform"></i>
                            MASUK KE SISTEM
                        </span>
                        <div
                            class="absolute inset-0 bg-primary/10 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left">
                        </div>
                    </button>
                </form>

                <!-- Enhanced Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white/95 text-gray-500 font-medium">Quick Links</span>
                    </div>
                </div>

                <!-- Enhanced Help Center Links -->
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" class="help-link group transform hover:scale-105 transition-all duration-300">
                        <i class="bi bi-book text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Panduan Pengguna</span>
                    </a>
                    <a href="https://wa.me/6283182192666?text=min%20mau%20joki%20nih%20bisa%20bantu%20nggak%2Cadmin%20ganteng%20deh"
                        class="help-link group transform hover:scale-105 transition-all duration-300">
                        <i class="bi bi-headset text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Pusat Bantuan</span>
                    </a>
                </div>
            </div>

            <!-- Enhanced Footer Text -->
            <p class="mt-8 text-center text-sm text-white/90 font-medium">
                &copy; {{ date('Y') }} Universitas. All rights reserved.
            </p>
        </div>
    </div>

    <style>
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

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
