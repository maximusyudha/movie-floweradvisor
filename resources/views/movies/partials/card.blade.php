<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition group">
    <a href="{{ route('movies.show', $movie['imdbID']) }}" class="block">
        <div class="relative overflow-hidden">
            @if ($movie['Poster'] && $movie['Poster'] !== 'N/A')
                <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}"
                     loading="lazy"
                     class="w-full h-44 xs:h-52 sm:h-64 object-cover rounded-t-xl group-hover:scale-105 transition duration-300">
            @else
                <div class="w-full h-44 xs:h-52 sm:h-64 bg-gray-200 rounded-t-xl flex items-center justify-center text-4xl xs:text-5xl">🎬</div>
            @endif
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition"></div>
            <div class="absolute bottom-2 right-2">
                <span class="bg-black/70 text-white text-xs px-2 py-0.5 rounded-full capitalize">
                    {{ $movie['Type'] ?? 'movie' }}
                </span>
            </div>
        </div>
    </a>
    <div class="p-3">
        <a href="{{ route('movies.show', $movie['imdbID']) }}" class="block">
            <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 mb-1 hover:text-indigo-600 transition">
                {{ $movie['Title'] }}
            </h3>
            <p class="text-gray-400 text-xs">{{ $movie['Year'] ?? 'N/A' }}</p>
        </a>
        <div class="mt-2 flex gap-1.5">
            @if ($movie['is_favorite'] ?? false)
                <button onclick="removeFavorite('{{ $movie['imdbID'] }}', this)"
                        class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 py-1.5 rounded-lg text-xs font-semibold transition border border-red-200">
                    ❌ {{ __('movies.remove_favorite') }}
                </button>
            @else
                <button onclick="addFavorite('{{ $movie['imdbID'] }}', &quot;{{ addslashes($movie['Title']) }}&quot;, '{{ $movie['Year'] ?? '' }}', '{{ $movie['Poster'] ?? '' }}', '{{ $movie['Type'] ?? '' }}', this)"
                        class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-1.5 rounded-lg text-xs font-semibold transition border border-indigo-200">
                    ❤️ {{ __('movies.add_favorite') }}
                </button>
            @endif
        </div>
    </div>
</div>
