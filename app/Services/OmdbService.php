<?php

namespace App\Services;

class OmdbService
{
    /**
     * OMDb API base URL.
     */
    const BASE_URL = 'http://www.omdbapi.com/';

    /**
     * API key from environment configuration.
     */
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OMDB_API_KEY', '73bc5fd5');
    }

    /**
     * Search for movies by title with pagination and optional year filter.
     *
     * @param  string  $title  Movie title to search
     * @param  int     $page   Page number (default 1)
     * @param  string|null $year  Optional release year filter
     * @return array|null  Decoded API response or null on failure
     */
    public function search(string $title, int $page = 1, string $year = null)
    {
        $params = [
            'apikey' => $this->apiKey,
            's' => $title,
            'page' => max(1, $page),
        ];

        if (!empty($year)) {
            $params['y'] = $year;
        }

        return $this->request($params);
    }

    /**
     * Get full details of a movie by its IMDb ID.
     *
     * @param  string  $imdbId  IMDb ID (e.g. "tt0848228")
     * @return array|null  Decoded API response or null on failure
     */
    public function getById(string $imdbId)
    {
        $params = [
            'apikey' => $this->apiKey,
            'i' => $imdbId,
            'plot' => 'full',
        ];

        return $this->request($params);
    }

    /**
     * Perform the HTTP GET request to OMDb and return decoded JSON.
     *
     * @param  array  $params  Query parameters
     * @return array|null  Decoded response or null if request fails
     */
    protected function request(array $params)
    {
        $url = self::BASE_URL . '?' . http_build_query($params);

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header' => "User-Agent: MovieApp/1.0\r\n",
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        return json_decode($response, true);
    }
}
