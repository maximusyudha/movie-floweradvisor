@extends('layouts.app')
@section('title', $movie['Title'] ?? __('detail.title'))

@section('content')

{{-- Back Button --}}
<div class="mb-4">
    <a href="{{ route('movies') }}"
       class="inline-flex items-center gap-1.5 sm:gap-2 text-indigo-600 hover:text-indigo-800 font-medium transition text-sm sm:text-base">
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('detail.back') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

    {{-- Poster Column --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden lg:sticky lg:top-20">
            @if ($movie['Poster'] && $movie['Poster'] !== 'N/A')
                <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}"
                     class="w-full object-cover" loading="lazy">
            @else
                <div class="w-full h-64 sm:h-80 lg:h-96 bg-gray-200 flex items-center justify-center text-6xl sm:text-8xl">🎬</div>
            @endif

            {{-- Favorite Action --}}
            <div class="p-4 sm:p-5 border-t border-gray-100">
                <div id="favStatus">
                    @if ($isFavorite)
                        <button onclick="removeFavoriteDetail()"
                                class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100
                                       text-red-600 py-3 rounded-xl font-semibold transition border border-red-200">
                            ❌ {{ __('movies.remove_favorite') }}
                        </button>
                    @else
                        <button onclick="addFavoriteDetail()"
                                class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700
                                       text-white py-3 rounded-xl font-semibold transition shadow-md hover:shadow-lg">
                            ❤️ {{ __('movies.add_favorite') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Info Column --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Title & Rating --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">{{ $movie['Title'] }}</h1>
                    <p class="text-gray-500 flex flex-wrap items-center gap-1.5 sm:gap-3 text-xs sm:text-sm">
                        <span>{{ $movie['Year'] ?? 'N/A' }}</span>
                        <span>•</span>
                        <span class="capitalize">{{ $movie['Type'] ?? 'movie' }}</span>
                        <span>•</span>
                        <span>{{ $movie['Runtime'] ?? 'N/A' }}</span>
                    </p>
                </div>
                @if (isset($movie['imdbRating']) && $movie['imdbRating'] !== 'N/A')
                    <div class="flex-shrink-0 bg-yellow-50 border border-yellow-200 rounded-xl px-3 sm:px-4 py-2 sm:py-3 text-center">
                        <div class="text-xl sm:text-2xl font-bold text-yellow-600">{{ $movie['imdbRating'] }}</div>
                        <div class="text-xs text-yellow-500">{{ $movie['imdbVotes'] ?? '' }}</div>
                    </div>
                @endif
            </div>

            {{-- Genre Tags --}}
            @if (!empty($movie['Genre']) && $movie['Genre'] !== 'N/A')
                <div class="flex flex-wrap gap-2">
                    @foreach (explode(', ', $movie['Genre']) as $genre)
                        <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-medium">
                            {{ trim($genre) }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Plot --}}
        @if (!empty($movie['Plot']) && $movie['Plot'] !== 'N/A')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-2 sm:mb-3 flex items-center gap-2">
                    📖 {{ __('detail.plot') }}
                </h2>
                <p class="text-gray-600 leading-relaxed text-sm sm:text-base">{{ $movie['Plot'] }}</p>
            </div>
        @endif

        {{-- Meta Info Grid --}}
        <div class="grid grid-cols-1 xs:grid-cols-2 gap-3 sm:gap-4">
            @foreach ([
                'director' => $movie['Director'] ?? null,
                'writer' => $movie['Writer'] ?? null,
                'actors' => $movie['Actors'] ?? null,
                'language' => $movie['Language'] ?? null,
                'country' => $movie['Country'] ?? null,
                'released' => $movie['Released'] ?? null,
                'dvd' => $movie['DVD'] ?? null,
                'box_office' => $movie['BoxOffice'] ?? null,
                'production' => $movie['Production'] ?? null,
                'website' => $movie['Website'] ?? null,
            ] as $key => $value)
                @if ($value && $value !== 'N/A')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                            {{ __("detail.$key") }}
                        </dt>
                        <dd class="text-gray-800 text-sm font-medium">{{ $value }}</dd>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Awards --}}
        @if (!empty($movie['Awards']) && $movie['Awards'] !== 'N/A')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-2 sm:mb-3 flex items-center gap-2">
                    🏆 {{ __('detail.awards') }}
                </h2>
                <p class="text-gray-600 text-sm sm:text-base">{{ $movie['Awards'] }}</p>
            </div>
        @endif

        {{-- Ratings --}}
        @if (!empty($movie['Ratings']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                    ⭐ {{ __('detail.rating') }}
                </h2>
                <div class="space-y-3">
                    @foreach ($movie['Ratings'] as $rating)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">{{ $rating['Source'] }}</span>
                            <span class="font-bold text-gray-800">{{ $rating['Value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const imdbId = '{{ $movie['imdbID'] }}';
const title = '{{ addslashes($movie['Title']) }}';
const year = '{{ $movie['Year'] ?? '' }}';
const poster = '{{ $movie['Poster'] ?? '' }}';
const type = '{{ $movie['Type'] ?? '' }}';

function addFavoriteDetail() {
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
            document.getElementById('favStatus').innerHTML = `
                <button onclick="removeFavoriteDetail()"
                        class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100
                               text-red-600 py-3 rounded-xl font-semibold transition border border-red-200">
                    ❌ {{ __('movies.remove_favorite') }}
                </button>`;
        }
    });
}

function removeFavoriteDetail() {
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
            document.getElementById('favStatus').innerHTML = `
                <button onclick="addFavoriteDetail()"
                        class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700
                               text-white py-3 rounded-xl font-semibold transition shadow-md hover:shadow-lg">
                    ❤️ {{ __('movies.add_favorite') }}
                </button>`;
        }
    });
}
</script>
@endsection
