<?php

namespace App\Http\Controllers;

use App\Services\OmdbService;
use App\FavoriteMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MovieController extends Controller
{
    /**
     * Default search keyword for Popular Movies when no query is provided.
     */
    const DEFAULT_QUERY = 'movie';

    protected $omdbService;

    public function __construct(OmdbService $omdbService)
    {
        $this->omdbService = $omdbService;
    }

    /**
     * Display the movie list with search, filter, and infinite scroll.
     */
    public function index(Request $request)
    {
        $this->middleware('auth');

        $query = $request->input('q', self::DEFAULT_QUERY);
        $year = $request->input('y', '');
        $page = max(1, (int) $request->input('page', 1));
        $isDefault = !$request->has('q');

        $movies = [];
        $totalResults = 0;
        $error = null;

        $result = $this->omdbService->search($query, $page, $year ?: null);

        if (isset($result['Response']) && $result['Response'] === 'True') {
            $movies = $result['Search'] ?? [];
            $totalResults = isset($result['totalResults']) ? (int) $result['totalResults'] : 0;
        } else {
            $error = $result['Error'] ?? 'No results found.';
        }

        $this->markFavoritesForMovies($movies);

        return view('movies.index', compact(
            'movies', 'query', 'year', 'page', 'totalResults', 'error', 'isDefault'
        ));
    }

    /**
     * Display the movie detail page.
     */
    public function show($id)
    {
        $this->middleware('auth');

        $movie = $this->omdbService->getById($id);

        if (!$movie || !isset($movie['imdbID'])) {
            abort(404);
        }

        $isFavorite = false;
        if (Session::has('user')) {
            $user = Session::get('user');
            $isFavorite = FavoriteMovie::where('user_id', $user['id'])
                                      ->where('imdb_id', $id)
                                      ->exists();
        }

        return view('movies.show', compact('movie', 'isFavorite'));
    }

    /**
     * AJAX endpoint for infinite scroll: fetch next page of results.
     */
    public function searchApi(Request $request)
    {
        $this->middleware('auth');

        $query = $request->input('q', '');
        $year = $request->input('y', '');
        $page = max(1, (int) $request->input('page', 1));

        if (empty($query)) {
            $query = self::DEFAULT_QUERY;
        }

        $result = $this->omdbService->search($query, $page, $year ?: null);

        if (isset($result['Response']) && $result['Response'] === 'True') {
            $movies = $result['Search'] ?? [];
            $totalResults = isset($result['totalResults']) ? (int) $result['totalResults'] : 0;

            $this->markFavoritesForMovies($movies);

            return response()->json([
                'movies' => $movies,
                'totalResults' => $totalResults,
                'error' => null,
            ]);
        }

        return response()->json([
            'movies' => [],
            'totalResults' => 0,
            'error' => $result['Error'] ?? 'No results.',
        ]);
    }

    /**
     * Annotate each movie with an is_favorite flag based on the current user's favorites.
     */
    private function markFavoritesForMovies(array &$movies): void
    {
        if (!Session::has('user') || empty($movies)) {
            return;
        }

        $user = Session::get('user');
        $favoriteIds = FavoriteMovie::where('user_id', $user['id'])
                                    ->pluck('imdb_id')
                                    ->toArray();

        foreach ($movies as $i => $movie) {
            $movies[$i]['is_favorite'] = in_array($movie['imdbID'], $favoriteIds);
        }
    }
}
