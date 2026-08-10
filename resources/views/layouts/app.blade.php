<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Movie App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fixed navbar */
        body { padding-top: 0; }

        /* Sticky footer: flex column layout */
        html, body { height: 100%; }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main { flex: 1 0 auto; }
        footer { flex-shrink: 0; }

        /* Animated search bar in navbar */
        #navSearchContainer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.25s ease;
            opacity: 0;
        }
        #navSearchContainer.open {
            max-height: 120px;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100">

    {{-- Navbar (fixed at top) --}}
    <nav class="fixed top-0 inset-x-0 bg-indigo-600 text-white shadow-lg z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('movies') }}" class="text-xl font-bold hover:text-indigo-200 transition">
                        🎬 MovieApp
                    </a>
                    @if (Session::has('user'))
                        <a href="{{ route('movies') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500 transition {{ request()->routeIs('movies') ? 'bg-indigo-700' : '' }}">
                            {{ __('nav.movies') }}
                        </a>
                        <a href="{{ route('favorites') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500 transition {{ request()->routeIs('favorites') ? 'bg-indigo-700' : '' }}">
                            {{ __('nav.favorites') }}
                        </a>
                    @endif
                </div>
                <div class="flex items-center space-x-3">
                    @if (Session::has('user'))
                        {{-- Search Toggle Button --}}
                        <button id="navSearchToggle" type="button"
                                class="p-2 rounded-md hover:bg-indigo-500 transition"
                                aria-label="Toggle search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>

                        {{-- Language Switcher --}}
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('lang.switch', 'en') }}"
                               class="px-2 py-1 rounded text-xs font-medium transition {{ app()->getLocale() == 'en' ? 'bg-white text-indigo-600' : 'bg-indigo-500 hover:bg-indigo-400' }}">
                                EN
                            </a>
                            <a href="{{ route('lang.switch', 'id') }}"
                               class="px-2 py-1 rounded text-xs font-medium transition {{ app()->getLocale() == 'id' ? 'bg-white text-indigo-600' : 'bg-indigo-500 hover:bg-indigo-400' }}">
                                ID
                            </a>
                        </div>

                        {{-- User Info --}}
                        <span class="hidden sm:inline text-sm text-indigo-200">
                            {{ __('nav.welcome') }}, <strong>{{ Session::get('user')['username'] }}</strong>
                        </span>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="bg-indigo-700 hover:bg-indigo-800 px-3 py-1 rounded text-sm font-medium transition">
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Search Bar (closed by default — opens via toggle) --}}
            @if (Session::has('user'))
                <div id="navSearchContainer">
                    <form id="navSearchForm" action="{{ route('movies') }}" method="GET" class="pb-3">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="flex-1 relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-indigo-300 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </span>
                                <input type="text" id="navSearchQuery" name="q"
                                       value=""
                                       class="w-full pl-9 pr-4 py-2 bg-indigo-700/50 border border-indigo-400
                                              text-white placeholder-indigo-200 rounded-lg
                                              focus:ring-2 focus:ring-white focus:border-white outline-none
                                              transition"
                                       placeholder="{{ __('movies.search_placeholder') }}" autocomplete="off">
                            </div>
                            <input type="text" id="navYearFilter" name="y" value="{{ $year ?? '' }}"
                                   class="w-full sm:w-32 px-3 py-2 bg-indigo-700/50 border border-indigo-400
                                          text-white placeholder-indigo-200 rounded-lg
                                          focus:ring-2 focus:ring-white focus:border-white outline-none transition"
                                   placeholder="{{ __('movies.year_placeholder') }}">
                            <button type="submit"
                                    class="bg-white text-indigo-600 hover:bg-indigo-100 px-5 py-2 rounded-lg
                                           font-semibold transition shadow-md whitespace-nowrap">
                                {{ __('movies.search_btn') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full pt-16">
        @yield('content')
    </main>

    {{-- Sticky Footer --}}
    <footer class="bg-gray-800 text-gray-400 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} MovieApp. Powered by OMDb API.</p>
        </div>
    </footer>

    <script>
    (function() {
        const toggle = document.getElementById('navSearchToggle');
        const container = document.getElementById('navSearchContainer');
        const searchInput = document.getElementById('navSearchQuery');

        // Auto-open search bar if there's a real search query from URL
        (function() {
            const params = new URLSearchParams(window.location.search);
            const q = params.get('q');
            // Only auto-open for actual searches, not the default Popular Movies page
            if (container && q && q.trim().length > 0 && q.trim() !== 'movie') {
                if (searchInput) searchInput.value = q;
                container.classList.add('open');
            }
        })();

        // Toggle search bar
        if (toggle && container) {
            toggle.addEventListener('click', function() {
                container.classList.toggle('open');
                if (container.classList.contains('open')) {
                    setTimeout(function() { searchInput.focus(); }, 200);
                }
            });
        }

        // Keyboard shortcut: "/" to open search, "Escape" to close
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                container.classList.add('open');
                setTimeout(function() { searchInput.focus(); }, 200);
            }
            if (e.key === 'Escape') {
                container.classList.remove('open');
            }
        });
    })();
    </script>

    @yield('scripts')

</body>
</html>
