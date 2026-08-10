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
        <div class="px-4 lg:px-6">
            <div class="flex items-center justify-between h-14">
                {{-- Logo --}}
                <a href="{{ route('movies') }}" class="text-lg font-bold hover:text-indigo-200 transition flex-shrink-0">
                    🎬 MovieApp
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden sm:flex items-center space-x-4">
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

                {{-- Right Actions --}}
                <div class="flex items-center space-x-2 sm:space-x-3">
                    @if (Session::has('user'))
                        {{-- Hamburger (mobile only) --}}
                        <button id="mobileMenuToggle" type="button"
                                class="sm:hidden p-2 rounded-md hover:bg-indigo-500 transition"
                                aria-label="Menu">
                            <svg id="hamburgerIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <button id="navSearchToggle" type="button"
                                class="p-2 rounded-md hover:bg-indigo-500 transition"
                                aria-label="Toggle search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>

                        <div class="hidden xs:flex items-center space-x-1">
                            <a href="{{ route('lang.switch', 'en') }}"
                               class="px-2 py-1 rounded text-xs font-medium transition {{ app()->getLocale() == 'en' ? 'bg-white text-indigo-600' : 'bg-indigo-500 hover:bg-indigo-400' }}">
                                EN
                            </a>
                            <a href="{{ route('lang.switch', 'id') }}"
                               class="px-2 py-1 rounded text-xs font-medium transition {{ app()->getLocale() == 'id' ? 'bg-white text-indigo-600' : 'bg-indigo-500 hover:bg-indigo-400' }}">
                                ID
                            </a>
                        </div>

                        <span class="hidden lg:inline text-sm text-indigo-200">
                            {{ __('nav.welcome') }}, <strong>{{ Session::get('user')['username'] }}</strong>
                        </span>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="bg-indigo-700 hover:bg-indigo-800 px-2 sm:px-3 py-1 rounded text-xs sm:text-sm font-medium transition whitespace-nowrap">
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Mobile Menu (hidden by default) --}}
            @if (Session::has('user'))
                <div id="mobileMenu" class="hidden sm:hidden pb-3 space-y-1">
                    <a href="{{ route('movies') }}"
                       class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500 transition {{ request()->routeIs('movies') ? 'bg-indigo-700' : '' }}">
                        {{ __('nav.movies') }}
                    </a>
                    <a href="{{ route('favorites') }}"
                       class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500 transition {{ request()->routeIs('favorites') ? 'bg-indigo-700' : '' }}">
                        {{ __('nav.favorites') }}
                    </a>
                    <div class="flex items-center space-x-1 px-3 py-2">
                        <a href="{{ route('lang.switch', 'en') }}"
                           class="px-2 py-1 rounded text-xs font-medium transition {{ app()->getLocale() == 'en' ? 'bg-white text-indigo-600' : 'bg-indigo-500 hover:bg-indigo-400' }}">
                            EN
                        </a>
                        <a href="{{ route('lang.switch', 'id') }}"
                           class="px-2 py-1 rounded text-xs font-medium transition {{ app()->getLocale() == 'id' ? 'bg-white text-indigo-600' : 'bg-indigo-500 hover:bg-indigo-400' }}">
                            ID
                        </a>
                    </div>
                    <span class="block px-3 py-2 text-sm text-indigo-200">
                        {{ __('nav.welcome') }}, <strong>{{ Session::get('user')['username'] }}</strong>
                    </span>
                </div>
            @endif

            {{-- Search Bar --}}
            @if (Session::has('user'))
                <div id="navSearchContainer">
                    <form id="navSearchForm" action="{{ route('movies') }}" method="GET" class="pb-3">
                        <div class="flex flex-col gap-2">
                            <div class="relative">
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
                                              transition text-sm"
                                       placeholder="{{ __('movies.search_placeholder') }}" autocomplete="off">
                            </div>
                            <div class="flex gap-2">
                                <input type="text" id="navYearFilter" name="y" value="{{ $year ?? '' }}"
                                       class="flex-1 sm:flex-none sm:w-24 px-3 py-2 bg-indigo-700/50 border border-indigo-400
                                              text-white placeholder-indigo-200 rounded-lg
                                              focus:ring-2 focus:ring-white focus:border-white outline-none transition text-sm"
                                       placeholder="{{ __('movies.year_placeholder') }}">
                                <button type="submit"
                                        class="bg-white text-indigo-600 hover:bg-indigo-100 px-4 py-2 rounded-lg
                                               font-semibold transition shadow-md whitespace-nowrap text-sm">
                                    {{ __('movies.search_btn') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="px-4 lg:px-8 py-6 w-full pt-14">
        @yield('content')
    </main>

    {{-- Sticky Footer --}}
    <footer class="bg-gray-800 text-gray-400 py-5 mt-auto">
        <div class="px-4 text-center text-xs sm:text-sm">
            <p>&copy; {{ date('Y') }} MovieApp. Powered by OMDb API.</p>
        </div>
    </footer>

    <script>
    (function() {
        const toggle = document.getElementById('navSearchToggle');
        const container = document.getElementById('navSearchContainer');
        const searchInput = document.getElementById('navSearchQuery');
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const closeIcon = document.getElementById('closeIcon');

        // Mobile menu toggle
        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                hamburgerIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
            // Close menu when clicking a link
            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.add('hidden');
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                });
            });
        }

        // Auto-open search bar if there's a real search query from URL
        (function() {
            const params = new URLSearchParams(window.location.search);
            const q = params.get('q');
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
