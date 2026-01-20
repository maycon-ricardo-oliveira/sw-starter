<?php


namespace App\Services;

use App\DTO\Metrics\MetricsResponseDTO;
use App\Repositories\Contracts\MetricsRepositoryInterface;

class MetricsService
{
    private const EVENTS_KEY   = 'metrics:events';
    private const SNAPSHOT_KEY = 'metrics:snapshot';
    private const SNAPSHOT_TTL = 3600;

    public function __construct(
        private MetricsRepositoryInterface $snapshot
    ) {}

    public function getMetrics(): MetricsResponseDTO
    {
        $snapshot = $this->snapshot->latest(self::SNAPSHOT_KEY);

        if ($snapshot) {
            return MetricsResponseDTO::fromSnapshot($snapshot);
        }

        $computed = $this->recompute();

        if ($computed) {
            return MetricsResponseDTO::fromSnapshot($computed);
        }

        return MetricsResponseDTO::empty();
    }

    public function recordEvent(
        string $event,
        string $type,
        array $payload = [],
        ?float $durationMs = null
    ): void {
        $this->snapshot->append(self::EVENTS_KEY, [
            'event'       => $event,
            'type'        => $type,
            'payload'     => $payload,
            'duration_ms' => $durationMs ? round($durationMs, 2) : null,
            'created_at'  => now()->toIso8601String(),
        ]);
    }

    public function recompute(): array
    {
        $rawEvents = $this->snapshot->all(self::EVENTS_KEY);

        $events = array_values(array_filter(
            array_map(function ($event) {
                if (is_string($event)) {
                    return json_decode($event, true);
                }

                return $event;
            }, $rawEvents)
        ));

        $metrics = [
            'totalSearchesByType' => $this->computeTotalSearchesByType($events),
            'topTerms'            => $this->computeTopTerms($events),
            'dailySearches'       => $this->computeDailySearches($events),
            'lastSearches'        => $this->computeLastSearches($events),
            'averageRequestTimeMs'=> $this->computeAverageRequestTime($events),
            'updatedAt'           => now()->toIso8601String(),
        ];

        $this->snapshot->save(self::SNAPSHOT_KEY, $metrics, self::SNAPSHOT_TTL);

        return $metrics;
    }

    private function computeTotalSearchesByType(array $events): array
    {
        $result = ['people' => 0, 'movie' => 0];

        foreach ($events as $event) {

            if (($event['event'] ?? null) === 'search') {
                $type = $event['type'] ?? null;

                if (isset($result[$type])) {
                    $result[$type]++;
                }
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
