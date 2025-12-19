<?php

namespace App\Services;

use App\Domain\PeopleDomain;
use App\DTO\People\PeopleResponseDTO;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Services\Contracts\SearchServiceInterface;
use Illuminate\Support\Facades\Cache;

class PeopleService implements SearchServiceInterface
{
    private SearchRepositoryInterface $peopleRepo;

    public function __construct(SearchRepositoryInterface $peopleRepo) {
        $this->peopleRepo = $peopleRepo;
    }

    private const SEARCH_TTL = 600;   // 10 minuts
    private const DETAIL_TTL = 3600;  // 1 hour

    public function search($term): array
    {

        $cacheKey = "people:search:{$term}";

        return Cache::remember(
            $cacheKey,
            self::SEARCH_TTL,
            function () use ($term) {
                $response = $this->peopleRepo->search($term);
                return array_map(
                    fn ($item) => $this->convertToDTO($item),
                    $response
                );
            }
        );
    }

    public function details(string|int $id): PeopleResponseDTO
    {
        $cacheKey = "people:details:{$id}";

        return Cache::remember(
            $cacheKey,
            self::DETAIL_TTL,
            function () use ($id) {
                $data = $this->peopleRepo->find($id);
                return $this->convertToDTO($data);
            }
        );
    }

    protected function convertToDTO($people): PeopleResponseDTO
    {
        $peopleDomain = $this->convertRecordToDomain($people);
        return new PeopleResponseDTO($peopleDomain);
    }

    protected function convertRecordToDomain($people): PeopleDomain
    {
        $properties = $people['properties'];
        return new PeopleDomain(
            id: $people['uid'],
            name: $properties['name'],
            gender: $properties['gender'] ?? 'unknown',
            skinColor: $properties['skin_color'] ?? '',
            hairColor: $properties['hair_color'] ?? '',
            eyeColor: $properties['eye_color'] ?? '',
            height: is_numeric($properties['height']) ? (int) $properties['height'] : null,
            mass: $properties['mass'] ?? null,
            birthYear: $properties['birth_year'] ?? null,
            homeworld: $properties['homeworld'],
            movies: $properties['films'] ?? [],
            vehicles: $properties['vehicles'] ?? [],
            starships: $properties['starships'] ?? [],
            createdAt: $properties['created'],
            updatedAt: $properties['edited'],
            url: $properties['url']
        );
    }
}