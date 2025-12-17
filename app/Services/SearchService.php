<?php

namespace App\Services;

use App\Enums\SearchTypeEnum;
use App\Exceptions\SearchException;
use App\Services\Contracts\SearchServiceInterface;

class SearchService
{
    public function __construct(
        private SearchServiceInterface $peopleService,
        private SearchServiceInterface $movieService
    ) {}

    /**
     * Search by term (people or movies)
     * @throws SearchException
     */
    public function search(SearchTypeEnum $type, string $term): array
    {
        return match ($type) {
            SearchTypeEnum::PEOPLE => $this->peopleService->search($term),
            SearchTypeEnum::MOVIES => $this->movieService->search($term),
            default => throw new SearchException('Invalid search type'),
        };
    }

    /**
     * Find details by ID (people or movies)
     * @throws SearchException
     */
    public function details(SearchTypeEnum $type, string $id)
    {
        return match ($type) {
            SearchTypeEnum::PEOPLE => $this->peopleService->details($id),
            SearchTypeEnum::MOVIES => $this->movieService->details($id),
            default => throw new SearchException('Invalid search type'),
        };
    }
}
