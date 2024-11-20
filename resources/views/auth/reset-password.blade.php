@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
    <!-- Tambahkan FontAwesome CDN di bagian atas -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <div class="min-h-screen flex items-center justify-center bg-primary">
        <div class="w-full max-w-md px-6">
            <div class="bg-white rounded-[32px] shadow-2xl p-8">
                <div class="text-center space-y-3 mb-8">
                    <h1 class="text-[32px] font-bold text-primary">Reset Password</h1>
                    <p class="text-gray-600 text-[16px]">Please enter your new password</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email -->
                    <div>
                        <input type="email" name="email"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-full
                               focus:ring-0 focus:border-gray-200 text-gray-600"
                            placeholder="Enter your email" value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-full
                               focus:ring-0 focus:border-gray-200 text-gray-600"
                            placeholder="Enter new password">
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="password-icon" class="fas fa-eye-slash text-xl"></i>
                        </button>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-full
                               focus:ring-0 focus:border-gray-200 text-gray-600"
                            placeholder="Confirm new password">
                        <button type="button" onclick="togglePassword('password_confirmation')"
                            class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="password_confirmation-icon" class="fas fa-eye-slash text-xl"></i>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold 
                           py-4 px-6 rounded-full transition duration-200 mt-4">
                        Reset Password
                    </button>

                    <!-- Back Link -->
                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark">
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword(inputId) {
                const passwordInput = document.getElementById(inputId);
                const icon = document.getElementById(inputId + '-icon');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            }
        </script>
    @endpush
@endsection
