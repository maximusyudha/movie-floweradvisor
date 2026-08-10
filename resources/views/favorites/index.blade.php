@extends('layouts.app')
@section('title', __('favorites.title'))

@section('content')

{{-- Page Header --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">{{ __('favorites.title') }}</h1>
</div>

{{-- Empty State --}}
@if ($favorites->isEmpty())
    <div class="text-center py-24">
        <div class="text-8xl mb-5">💔</div>
        <h3 class="text-2xl font-bold text-gray-600 mb-3">{{ __('favorites.empty') }}</h3>
        <p class="text-gray-400 mb-8">{{ __('favorites.empty_desc') }}</p>
        <a href="{{ route('movies') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                  px-6 py-3 rounded-xl font-semibold transition shadow-md hover:shadow-lg">
            🔍 {{ __('nav.movies') }}
        </a>
    </div>
@else

    {{-- Favorites Grid --}}
    <div id="favGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @foreach ($favorites as $fav)
            <div id="fav-card-{{ $fav->imdb_id }}"
                 class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition group">
                <a href="{{ route('movies.show', $fav->imdb_id) }}" class="block">
                    <div class="relative overflow-hidden">
                        @if ($fav->poster && $fav->poster !== 'N/A')
                            <img src="{{ $fav->poster }}" alt="{{ $fav->title }}"
                                 loading="lazy"
                                 class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center text-5xl">🎬</div>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition"></div>
                    </div>
                </a>
                <div class="p-3">
                    <a href="{{ route('movies.show', $fav->imdb_id) }}" class="block">
                        <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 mb-1 hover:text-indigo-600 transition">
                            {{ $fav->title }}
                        </h3>
                        <p class="text-gray-400 text-xs">{{ $fav->year ?? 'N/A' }}</p>
                        <p class="text-gray-300 text-xs mt-0.5">{{ $fav->type ?? 'movie' }}</p>
                    </a>
                    <p class="text-gray-300 text-xs mt-2">
                        {{ __('favorites.added_on') }}: {{ $fav->created_at->format('d M Y') }}
                    </p>
                    <div class="mt-2 flex gap-1.5">
                        <a href="{{ route('movies.show', $fav->imdb_id) }}"
                           class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-1.5 rounded-lg text-xs font-semibold transition border border-indigo-200 text-center">
                            📖 {{ __('favorites.view_detail') }}
                        </a>
                        <button onclick="removeFavorite('{{ $fav->imdb_id }}', '{{ $fav->title }}')"
                                class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold transition border border-red-200">
                            🗑️
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endif

@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.removeFavorite = function(imdbId, title) {
    if (!confirm('Remove "' + title + '" from favorites?')) return;

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
            const card = document.getElementById('fav-card-' + imdbId);
            if (card) {
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    card.remove();
                    checkEmpty();
                }, 300);
            }
        }
    });
};

function checkEmpty() {
    const grid = document.getElementById('favGrid');
    if (grid && grid.children.length === 0) {
        location.reload();
    }
}
</script>
@endsection
