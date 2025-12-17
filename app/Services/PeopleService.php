<?php

namespace App\Services;

use App\Domain\PeopleDomain;
use App\DTO\People\PeopleResponseDTO;
use App\Repositories\Contracts\SearchRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class PeopleService
{
    private SearchRepositoryInterface $peopleRepo;

    public function __construct(SearchRepositoryInterface $peopleRepo) {
        $this->peopleRepo = $peopleRepo;
    }

    private const CACHE_TTL = 3600; // 1h

    public function details(string|int $id): PeopleResponseDTO
    {
        $cacheKey = "people:details:{$id}";

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
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

    protected function convertRecordToDomain($properties): PeopleDomain
    {
        return new PeopleDomain(
            name: $properties['name'],
            gender: $properties['gender'] ?? 'unknown',
            skinColor: $properties['skin_color'] ?? '',
            hairColor: $properties['hair_color'] ?? '',
            eyeColor: $properties['eye_color'] ?? '',
            height: is_numeric($properties['height']) ? (int) $properties['height'] : null,
            mass: $properties['mass'] ?? null,
            birthYear: $properties['birth_year'] ?? null,
            homeworld: $properties['homeworld'],
            films: $properties['films'] ?? [],
            vehicles: $properties['vehicles'] ?? [],
            starships: $properties['starships'] ?? [],
            createdAt: $properties['created'],
            updatedAt: $properties['edited'],
            url: $properties['url']
        );
    }
}