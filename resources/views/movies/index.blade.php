@extends('layouts.app')

@section('title', __('movies.title'))

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
            {{ $isDefault ? __('movies.popular') : __('movies.title') }}
        </h1>
        @if ($isDefault)
            <p class="text-gray-500 text-xs sm:text-sm mt-1">{{ __('movies.popular_desc') }}</p>
        @else
            <p class="text-gray-500 text-xs sm:text-sm mt-1">
                <span class="font-semibold text-gray-800">{{ $totalResults }}</span>
                {{ __('movies.results') }}
                @if ($year) <span class="text-gray-400"> ({{ $year }})</span> @endif
            </p>
        @endif
    </div>
    @if (!$isDefault && $totalResults > 0)
        <p class="text-xs text-gray-400 sm:text-sm">
            {{ __('movies.page_of', ['current' => $page, 'total' => ceil($totalResults / 10)]) }}
        </p>
    @endif
</div>

{{-- Empty State: no results --}}
@if (!$isDefault && empty($movies))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
        <div class="text-5xl sm:text-7xl mb-4">🎭</div>
        <h3 class="text-xl sm:text-2xl font-bold text-gray-700 mb-2">
            {{ !empty($error) ? $error : __('movies.no_results') }}
        </h3>
        <p class="text-gray-400 mb-6 text-sm sm:text-base">Try a different title or year</p>
        <a href="{{ route('movies') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                  px-5 sm:px-6 py-2 sm:py-2.5 rounded-lg font-semibold transition shadow-md text-sm sm:text-base">
            🔍 Browse Popular Movies
        </a>
    </div>
@endif

{{-- Movie Grid --}}
@if (!empty($movies))
<div id="movieGrid" class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-5">
    @foreach ($movies as $movie)
        @include('movies.partials.card', ['movie' => $movie])
    @endforeach
</div>
@endif

{{-- Load More / Infinite Scroll --}}
@if ($totalResults > count($movies))
<div id="loadMoreContainer" class="mt-8 text-center">
    <button id="loadMoreBtn"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg
                   font-semibold transition shadow-md hover:shadow-lg">
        {{ __('movies.load_more') }}
    </button>
    <p id="loadingText" class="hidden text-gray-500 mt-3">{{ __('movies.loading') }}</p>
</div>
@endif

{{-- Scroll Sentinel (for Intersection Observer) --}}
<div id="scrollSentinel" class="h-10"></div>

@endsection

@section('scripts')
<script>
(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const searchInput = document.getElementById('navSearchQuery');
    const yearInput = document.getElementById('navYearFilter');
    const searchForm = document.getElementById('navSearchForm');

    let currentQuery = searchInput ? searchInput.value : '';
    let currentYear = yearInput ? yearInput.value : '';
    let currentPage = {{ $page }};
    let totalResults = {{ $totalResults }};
    let isLoading = false;
    const perPage = 10;

    @if ($totalResults > count($movies))
        initInfiniteScroll();
    @endif

    function initInfiniteScroll() {
        const sentinel = document.getElementById('scrollSentinel');
        if (!sentinel) return;
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !isLoading && hasMorePages()) {
                loadMoreMovies();
            }
        }, { rootMargin: '200px' });
        observer.observe(sentinel);
    }

    function hasMorePages() {
        return (currentPage * perPage) < totalResults;
    }

    function loadMoreMovies() {
        if (isLoading || !hasMorePages()) return;
        isLoading = true;
        currentPage++;

        const btn = document.getElementById('loadMoreBtn');
        const loadingText = document.getElementById('loadingText');
        if (btn) btn.classList.add('hidden');
        if (loadingText) loadingText.classList.remove('hidden');

        fetch('{{ route("movies.search") }}?q=' + encodeURIComponent(currentQuery)
            + '&y=' + encodeURIComponent(currentYear)
            + '&page=' + currentPage, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('movieGrid');
            data.movies.forEach(movie => {
                grid.insertAdjacentHTML('beforeend', buildCard(movie));
            });
            if (loadingText) loadingText.classList.add('hidden');
            if (hasMorePages() && btn) {
                btn.classList.remove('hidden');
            }
            isLoading = false;
        })
        .catch(() => {
            currentPage--;
            if (btn) btn.classList.remove('hidden');
            if (loadingText) loadingText.classList.add('hidden');
            isLoading = false;
        });
    }

    function buildCard(movie) {
        const poster = movie.Poster && movie.Poster !== 'N/A'
            ? `<img src="${escapeHtml(movie.Poster)}" alt="${escapeHtml(movie.Title)}" loading="lazy" class="w-full h-44 xs:h-52 sm:h-64 object-cover">`
            : `<div class="w-full h-44 xs:h-52 sm:h-64 bg-gray-200 flex items-center justify-center text-4xl xs:text-5xl">🎬</div>`;

        const favBtn = movie.is_favorite
            ? `<button onclick="removeFavorite('${movie.imdbID}', this)"
                       class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 py-1.5 rounded-lg text-xs font-semibold transition border border-red-200">
                   ❌ {{ __('movies.remove_favorite') }}
               </button>`
            : `<button onclick="addFavorite('${movie.imdbID}', '${escapeHtml(movie.Title)}', '${escapeHtml(movie.Year || '')}', '${escapeHtml(movie.Poster || '')}', '${escapeHtml(movie.Type || '')}', this)"
                       class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-1.5 rounded-lg text-xs font-semibold transition border border-indigo-200">
                   ❤️ {{ __('movies.add_favorite') }}
               </button>`;

        return `
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition group">
            <a href="{{ route('movies.show', '') }}/${movie.imdbID}" class="block">
                <div class="relative overflow-hidden">
                    ${poster}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition"></div>
                    <div class="absolute bottom-2 right-2">
                        <span class="bg-black/70 text-white text-xs px-2 py-0.5 rounded-full capitalize">${movie.Type || 'movie'}</span>
                    </div>
                </div>
            </a>
            <div class="p-3">
                <a href="{{ route('movies.show', '') }}/${movie.imdbID}" class="block">
                    <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 mb-1 hover:text-indigo-600 transition">${escapeHtml(movie.Title)}</h3>
                    <p class="text-gray-400 text-xs">${escapeHtml(movie.Year || 'N/A')}</p>
                </a>
                <div class="mt-2 flex gap-1.5">
                    ${favBtn}
                </div>
            </div>
        </div>`;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/'/g, "\\'")
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    window.addFavorite = function(imdbId, title, year, poster, type, btn) {
        fetch('{{ route("favorites.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ imdb_id: imdbId, title, year, poster, type })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.outerHTML = `
                <button onclick="removeFavorite('${imdbId}', this)"
                           class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 py-1.5 rounded-lg text-xs font-semibold transition border border-red-200">
                       ❌ {{ __('movies.remove_favorite') }}
                   </button>`;
            }
        });
    };

    window.removeFavorite = function(imdbId, btn) {
        fetch('{{ route("favorites.destroy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ imdb_id: imdbId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.outerHTML = `
                <button onclick="addFavorite('${imdbId}', '', '', '', '', this)"
                           class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-1.5 rounded-lg text-xs font-semibold transition border border-indigo-200">
                       ❤️ {{ __('movies.add_favorite') }}
                   </button>`;
            }
        });
    };
})();
</script>
@endsection
