<?php

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('locale')->group(function () {

    // Login / Logout
    Route::get('/login', 'AuthController@showLoginForm')->name('login');
    Route::post('/login', 'AuthController@login')->name('login');
    Route::post('/logout', 'AuthController@logout')->name('logout');

    // Language Switch
    Route::get('/lang/{locale}', 'LanguageController@switch')->name('lang.switch');

    // Protected Routes (require authentication)
    Route::middleware('auth')->group(function () {

        // Movies
        Route::get('/movies', 'MovieController@index')->name('movies');
        Route::get('/movies/search', 'MovieController@searchApi')
            ->middleware('throttle:60,1')
            ->name('movies.search');
        Route::get('/movies/{id}', 'MovieController@show')->name('movies.show');

        // Favorites
        Route::get('/favorites', 'FavoriteController@index')->name('favorites');
        Route::post('/favorites', 'FavoriteController@store')
            ->middleware('throttle:30,1')
            ->name('favorites.store');
        Route::post('/favorites/remove', 'FavoriteController@destroy')
            ->middleware('throttle:30,1')
            ->name('favorites.destroy');
    });

});
