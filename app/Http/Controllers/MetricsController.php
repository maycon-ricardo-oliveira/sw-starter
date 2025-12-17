<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Redis;

class MetricsController extends Controller
{

    public function index()
    {
        /**
         * =========================
         * Total por tipo
         * =========================
         */
        $totalByType = [
            'people' => (int) Redis::get('metrics:search:type:people'),
            'movie'  => (int) Redis::get('metrics:search:type:movie'),
        ];

        /**
         * =========================
         * Top termos
         * =========================
         */
        $topPeopleTerms = Redis::zrevrange(
            'metrics:search:terms:people',
            0,
            4,
            ['withscores' => true]
        );

        $topMovieTerms = Redis::zrevrange(
            'metrics:search:terms:movie',
            0,
            4,
            ['withscores' => true]
        );

        /**
         * =========================
         * Buscas por dia (hoje)
         * =========================
         */
        $today = now()->format('Y-m-d');

        $dailySearches = [
            $today => (int) Redis::get("metrics:search:daily:{$today}")
        ];

        /**
         * =========================
         * Últimas buscas
         * =========================
         */
        $lastSearchesRaw = Redis::lrange('metrics:search:last', 0, 9);

        $lastSearches = array_map(
            fn ($item) => json_decode($item, true),
            $lastSearchesRaw
        );

        return response()->json([
            'totalSearchesByType' => $totalByType,
            'topTerms' => [
                'people' => $this->formatTerms($topPeopleTerms),
                'movie'  => $this->formatTerms($topMovieTerms),
            ],
            'dailySearches' => $dailySearches,
            'lastSearches' => $lastSearches,
        ]);
    }

    private function formatTerms(array $terms): array
    {
        $formatted = [];

        foreach ($terms as $term => $count) {
            $formatted[] = [
                'term' => $term,
                'count' => (int) $count,
            ];
        }

        return $formatted;
    }

}