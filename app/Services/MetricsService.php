<?php


namespace App\Services;

use Illuminate\Support\Facades\Redis;

class MetricsService
{

    public function recordEvent(
        string $event,
        string $type,
        array $payload = [],
        ?float $durationMs = null
    ): void {
        Redis::rpush('metrics:events', json_encode([
            'event'       => $event,           // search | details | etc
            'type'        => $type,            // people | movie
            'payload'     => $payload,         // term, id, endpoint...
            'duration_ms' => $durationMs ? round($durationMs, 2) : null,
            'created_at'  => now()->toIso8601String(),
        ]));
    }

    public function getMetrics(): array
    {
        $snapshot = Redis::get('metrics:snapshot');

        if (!$snapshot) {
            return [
                'message' => 'Metrics not computed yet',
                'data' => [],
            ];
        }

        return json_decode($snapshot, true);
    }

    /**
     * =========================
     * RECOMPUTE (COMMAND)
     * =========================
     */
    public function recompute(): array
    {
        $rawEvents = Redis::lrange('metrics:events', 0, -1);
        $events = array_map(fn ($e) => json_decode($e, true), $rawEvents);

        if (empty($events)) {
            return [];
        }

        $metrics = [
            'totalSearchesByType' => $this->computeTotalSearchesByType($events),
            'topTerms'            => $this->computeTopTerms($events),
            'dailySearches'       => $this->computeDailySearches($events),
            'lastSearches'        => $this->computeLastSearches($events),
            'averageRequestTimeMs'=> $this->computeAverageRequestTime($events),
            'updatedAt'           => now()->toIso8601String(),
        ];

        Redis::set('metrics:snapshot', json_encode($metrics));

        return $metrics;
    }

    /**
     * =========================
     * AUXILIARES
     * =========================
     */

    private function computeTotalSearchesByType(array $events): array
    {
        $result = ['people' => 0, 'movie' => 0];

        foreach ($events as $event) {
            if ($event['event'] === 'search') {
                $result[$event['type']]++;
            }
        }

        return $result;
    }

    private function computeTopTerms(array $events): array
    {
        $terms = [
            'people' => [],
            'movie'  => [],
        ];

        foreach ($events as $event) {
            if ($event['event'] !== 'search') {
                continue;
            }

            $term = strtolower($event['payload']['term'] ?? '');

            if (!$term) {
                continue;
            }

            $terms[$event['type']][$term] =
                ($terms[$event['type']][$term] ?? 0) + 1;
        }

        return [
            'people' => $this->formatTopTerms($terms['people']),
            'movie'  => $this->formatTopTerms($terms['movie']),
        ];
    }

    private function formatTopTerms(array $terms): array
    {
        arsort($terms);
        $top = array_slice($terms, 0, 5, true);
        $total = array_sum($top);

        return array_map(
            fn ($count, $term) => [
                'term'       => $term,
                'count'      => $count,
                'percentage' => $total > 0
                    ? round(($count / $total) * 100, 2)
                    : 0,
            ],
            $top,
            array_keys($top)
        );
    }

    private function computeDailySearches(array $events): array
    {
        $daily = [];

        foreach ($events as $event) {
            if ($event['event'] !== 'search') {
                continue;
            }

            $day = substr($event['created_at'], 0, 10);
            $daily[$day] = ($daily[$day] ?? 0) + 1;
        }

        return $daily;
    }

    private function computeLastSearches(array $events): array
    {
        $searches = array_values(
            array_filter($events, fn ($e) => $e['event'] === 'search')
        );

        $last = array_slice($searches, -10);

        return array_map(fn ($e) => [
            'type' => $e['type'],
            'term' => $e['payload']['term'] ?? null,
            'at'   => $e['created_at'],
        ], array_reverse($last));
    }

    private function computeAverageRequestTime(array $events): float
    {
        $durations = [];

        foreach ($events as $event) {
            if (!empty($event['duration_ms'])) {
                $durations[] = $event['duration_ms'];
            }
        }

        if (empty($durations)) {
            return 0;
        }

        return round(array_sum($durations) / count($durations), 2);
    }

}
