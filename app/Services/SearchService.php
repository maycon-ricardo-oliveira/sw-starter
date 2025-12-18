<?php

namespace App\Services;

use App\Enums\SearchTypeEnum;
use App\Exceptions\SearchException;
use App\Services\Contracts\SearchServiceInterface;

class SearchService
{
    public function __construct(
        private SearchServiceInterface $peopleService,
        private SearchServiceInterface $movieService,
        private MetricsService $metricsService
    ) {}

    /**
     * Search by term (people or movies)
     * @throws SearchException
     */
    public function search(SearchTypeEnum $type, string $term): array
    {
        $start = microtime(true);
        $result = match ($type) {
            SearchTypeEnum::PEOPLE => $this->peopleService->search($term),
            SearchTypeEnum::MOVIE => $this->movieService->search($term),
            default => throw new SearchException('Invalid search type'),
        };

        $durationMs = (microtime(true) - $start) * 1000;
        $this->metricsService->recordEvent(
            event: 'search',
            type: $type->value,
            payload: [
                'term' => $term,
            ],
            durationMs: $durationMs
        );

        return $result;
    }

    /**
     * Find details by ID (people or movies)
     * @throws SearchException
     */
    public function details(SearchTypeEnum $type, string $id)
    {
        $start = microtime(true);
        $result = match ($type) {
            SearchTypeEnum::PEOPLE => $this->peopleService->details($id),
            SearchTypeEnum::MOVIE => $this->movieService->details($id),
            default => throw new SearchException('Invalid search type'),
        };

        $durationMs = (microtime(true) - $start) * 1000;
        $this->metricsService->recordEvent(
            event: 'details',
            type: $type->value,
            payload: [
                'id' => $id,
            ],
            durationMs: $durationMs
        );
        return $result;
    }
}
