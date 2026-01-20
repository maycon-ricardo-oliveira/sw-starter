<?php

namespace App\Services;

use App\Domain\PeopleDomain;
use App\DTO\Movie\MovieLightDTO;
use App\DTO\People\PeopleResponseDTO;
use App\Repositories\Contracts\CacheRepositoryInterface;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Services\Contracts\SearchServiceInterface;

class PeopleService extends BaseService implements SearchServiceInterface
{
    private SearchRepositoryInterface $peopleRepo;
    private CacheRepositoryInterface $cache;

    public function __construct(
        SearchRepositoryInterface $peopleRepo,
        CacheRepositoryInterface $cache
    ) {
        $this->cache = $cache;
        $this->peopleRepo = $peopleRepo;
    }

    private const SEARCH_TTL = 600;   // 10 minuts
    private const DETAIL_TTL = 3600;  // 1 hour

    public function search($term): array
    {

        $cacheKey = "people:search:{$term}";

        return $this->cache->remember(
            $cacheKey,
            self::SEARCH_TTL,
            function () use ($term) {
                $response = $this->peopleRepo->search($term);
                return array_map(
                    function ($data) {
                        $domain = $this->convertRecordToDomain($data);
                        return $this->convertToDTO($domain);
                    }, $response
                );
            }
        );
    }

    public function details(string|int $id): PeopleResponseDTO
    {

        $cacheKey = "people:detail:{$id}";

        $data = $this->cache->remember(
            $cacheKey,
            self::DETAIL_TTL,
            fn () => $this->peopleRepo->find($id)
        );

        $movies = $this->getMoviesByIds($data);

        $domain = $this->convertRecordToDomain($data, $movies);
        return $this->convertToDTO($domain);
    }

    protected function convertToDTO(PeopleDomain $people): PeopleResponseDTO
    {
        return new PeopleResponseDTO($people);
    }

    protected function convertRecordToDomain($people, $related = null): PeopleDomain
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
            movies: $related ?? [],
            vehicles: $properties['vehicles'] ?? [],
            starships: $properties['starships'] ?? [],
            createdAt: $properties['created'],
            updatedAt: $properties['edited'],
            url: $properties['url']
        );
    }

    private function getMoviesByIds($data): array
    {

        $movieUrls = $data['properties']['films'] ?? [];
        $movieIds  = $this->extractIds($movieUrls);

        if (empty($movieIds)) {
            return [];
        }

        $response = $this->peopleRepo->findRelated($movieIds);

        return array_map(
            fn ($movie) => MovieLightDTO::make($movie)->toArray(),
            $response
        );
    }

}