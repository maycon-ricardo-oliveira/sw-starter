<?php

namespace App\Services;

use App\Domain\PeopleDomain;

use App\DTO\People\PeopleResponseDTO;
use App\Repositories\Contracts\SearchRepositoryInterface;
use InvalidArgumentException;

class SearchService
{
    private SearchRepositoryInterface $peopleRepo;

    public function __construct(SearchRepositoryInterface $peopleRepo) {
        $this->peopleRepo = $peopleRepo;
    }

    private function resolveRepository(string $type)
    {
        return match ($type) {
            'people' => $this->peopleRepo,
            'movies' => $this->peopleRepo,
            default => throw new InvalidArgumentException('Invalid search type')
        };
    }

    public function search(string $type, $query): array
    {
        $response = $this->resolveRepository($type)->search($query);

        return array_map(
            fn ($item) => $this->convertToDTO($item['properties']),
            $response
        );
    }



    public function details(string $type, string $id): PeopleResponseDTO
    {

        $response = $this->resolveRepository($type)->find($id);

        return $this->convertToDTO($response);
    }



}