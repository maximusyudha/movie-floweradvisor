@extends('layouts.app')

@section('title', __('auth.login_title'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-md">

        {{-- Logo / Branding --}}
        <div class="text-center mb-8">
            <div class="text-6xl mb-3">🎬</div>
            <h1 class="text-3xl font-bold text-gray-800">MovieApp</h1>
            <p class="text-gray-500 mt-1">{{ __('auth.login_title') }}</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Username --}}
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        {{ __('auth.username') }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <input type="text" id="username" name="username" required
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg
                                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      transition outline-none"
                               placeholder="username" autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        {{ __('auth.password') }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg
                                      focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                      transition outline-none"
                               placeholder="••••••••">
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                               py-2.5 rounded-lg transition shadow-md hover:shadow-lg">
                    {{ __('auth.login_btn') }}
                </button>
            </form>
        </div>

        {{-- Footer Note --}}
        <!-- <p class="text-center text-gray-400 text-xs mt-6">
            Demo credentials: <code class="bg-gray-200 px-1.5 py-0.5 rounded">aldmic</code> /
            <code class="bg-gray-200 px-1.5 py-0.5 rounded">123abc123</code>
        </p> -->
    </div>
</div>
@endsection
