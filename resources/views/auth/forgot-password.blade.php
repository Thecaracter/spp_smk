@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-primary">
        <div class="w-full max-w-md p-6">
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-primary mb-3">Forgot Password?</h1>
                    <p class="text-gray-600">Enter your email to reset your password</p>
                </div>

                @if (session('status'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50">
                        <span class="text-green-700">{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <input type="email" name="email"
                            class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl 
                                  focus:ring-primary focus:border-primary"
                            placeholder="Enter your email" value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold 
                               py-3.5 px-4 rounded-xl transition duration-200">
                        Send Reset Link
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark text-sm">
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
