<?php


namespace App\Services;

use Illuminate\Support\Facades\Redis;

class MetricsService
{
    public function registerSearch(
        string $type,
        string $term
    ): void {
        // =========================
        // Total por tipo
        // =========================
        Redis::incr("metrics:search:type:{$type}");

        // =========================
        // Top termos
        // =========================
        Redis::zincrby(
            "metrics:search:terms:{$type}",
            1,
            strtolower($term)
        );

        // =========================
        // Buscas por dia
        // =========================
        $today = now()->format('Y-m-d');
        Redis::incr("metrics:search:daily:{$today}");

        // =========================
        // Últimas buscas
        // =========================
        Redis::lpush(
            'metrics:search:last',
            json_encode([
                'type' => $type,
                'term' => $term,
                'at'   => now()->toISOString(),
            ])
        );

        // mantém só as 10 últimas
        Redis::ltrim('metrics:search:last', 0, 9);
    }

    public function getMetrics(): array
    {
        $today = now()->format('Y-m-d');

        return [
            'totalSearchesByType' => $this->getTotalByType(),
            'topTerms'            => $this->getTopTerms(),
            'dailySearches'       => $this->getDailySearches($today),
            'lastSearches'        => $this->getLastSearches(),
            'averageRequestTime' => $this->getAverageRequestTime(),
        ];
    }
    
    public function registerDetails(string $type, string $id): void
    {
        Redis::incr("metrics:details:type:{$type}");

        Redis::zincrby(
            "metrics:details:entity:{$type}",
            1,
            $id
        );

        $hour = now()->format('H');
        Redis::incr("metrics:details:hourly:{$hour}");
    }

    public function registerRequestTime($type, $endpoint, $durationMs)
    {
        Redis::lpush('metrics:request:times', json_encode([
            'type' => $type,
            'endpoint' => $endpoint,
            'duration' => (int) $durationMs,
            'timestamp' => now()->toISOString(),
        ]));
    }



    private function getTotalByType(): array
    {
        return [
            'people' => (int) Redis::get('metrics:search:type:people'),
            'movie'  => (int) Redis::get('metrics:search:type:movies'),
        ];
    }

    private function getTopTerms(): array
    {
        return [
            'people' => $this->formatTerms(
                Redis::zrevrange(
                    'metrics:search:terms:people',
                    0,
                    4,
                    ['withscores' => true]
                )
            ),
            'movie' => $this->formatTerms(
                Redis::zrevrange(
                    'metrics:search:terms:movies',
                    0,
                    4,
                    ['withscores' => true]
                )
            ),
        ];
    }

    private function getDailySearches(string $date): array
    {
        return [
            $date => (int) Redis::get("metrics:search:daily:{$date}")
        ];
    }

    private function getLastSearches(): array
    {
        $items = Redis::lrange('metrics:search:last', 0, 9);

        return array_map(
            fn ($item) => json_decode($item, true),
            $items
        );
    }

    private function formatTerms(array $terms): array
    {
        $formatted = [];

        foreach ($terms as $term => $count) {
            $formatted[] = [
                'term'  => $term,
                'count' => (int) $count,
            ];
        }

        return $formatted;
    }
    private function getAverageRequestTime(): array
    {
        $avg = Redis::get('metrics:request:avg');

        return $avg
            ? json_decode($avg, true)
            : ['average_ms' => 0];
    }

}
