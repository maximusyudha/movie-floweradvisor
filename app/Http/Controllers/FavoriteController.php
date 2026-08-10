<?php

namespace App\Http\Controllers;

use App\FavoriteMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FavoriteController extends Controller
{
    /**
     * Display the current user's favorite movies.
     */
    public function index()
    {
        $this->middleware('auth');

        $user = Session::get('user');
        $favorites = FavoriteMovie::where('user_id', $user['id'])
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Add a movie to the current user's favorites.
     */
    public function store(Request $request)
    {
        $this->middleware('auth');

        $validated = $request->validate([
            'imdb_id' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'year' => 'nullable|string|max:10',
            'poster' => 'nullable|string|max:500',
            'type' => 'nullable|string|max:50',
        ]);

        $user = Session::get('user');

        $alreadyExists = FavoriteMovie::where('user_id', $user['id'])
                                       ->where('imdb_id', $validated['imdb_id'])
                                       ->exists();

        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'message' => 'Already in favorites.',
            ]);
        }

        FavoriteMovie::create([
            'user_id' => $user['id'],
            'imdb_id' => $validated['imdb_id'],
            'title' => $validated['title'],
            'year' => $validated['year'] ?? null,
            'poster' => $validated['poster'] ?? null,
            'type' => $validated['type'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites.',
        ]);
    }

    /**
     * Remove a movie from the current user's favorites.
     */
    public function destroy(Request $request)
    {
        $this->middleware('auth');

        $validated = $request->validate([
            'imdb_id' => 'required|string|max:20',
        ]);

        $user = Session::get('user');

        FavoriteMovie::where('user_id', $user['id'])
                     ->where('imdb_id', $validated['imdb_id'])
                     ->delete();

        return response()->json(['success' => true]);
    }
}