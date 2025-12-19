<?php

namespace App\Services;

use App\Domain\MovieDomain;
use App\DTO\Movie\MovieResponseDTO;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Services\Contracts\SearchServiceInterface;
use Illuminate\Support\Facades\Cache;
use JsonSerializable;

class MovieService implements SearchServiceInterface
{

    private const SEARCH_TTL = 600;   // 10 minutes
    private const DETAIL_TTL = 3600;  // 1 hour
    private SearchRepositoryInterface $movieRepo;

    public function __construct(SearchRepositoryInterface $movieRepo) {
        $this->movieRepo = $movieRepo;
    }

    public function search(string $term)
    {
        $cacheKey = 'movie:search:' . strtolower($term);

        return Cache::remember(
            $cacheKey,
            self::SEARCH_TTL,
            function () use ($term) {
                $response = $this->movieRepo->search($term);

                return array_map(
                    fn ($item) => $this->convertToDTO($item),
                    $response ?? []
                );
            }
        );
    }

    public function details(string $id): JsonSerializable
    {
        $cacheKey = "movie:detail:$id";

        return Cache::remember(
            $cacheKey,
            self::DETAIL_TTL,
            function () use ($id) {
                $response = $this->movieRepo->find($id);
                return $this->convertToDTO($response);
            }
        );
    }

    protected function convertToDTO($movie): MovieResponseDTO
    {

        $movieDomain = $this->convertRecordToDomain($movie);
        return new MovieResponseDTO($movieDomain);
    }

    protected function convertRecordToDomain($movie): MovieDomain
    {
        $properties = $movie['properties'];
        return new MovieDomain(
            $movie['uid'],
            title: $properties['title'],
            episodeId: (int) $properties['episode_id'],
            director: $properties['director'],
            producer: $properties['producer'],
            releaseDate: $properties['release_date'],
            openingCrawl: $properties['opening_crawl'],
            characters: $properties['characters'] ?? [],
            planets: $properties['planets'] ?? [],
            starships: $properties['starships'] ?? [],
            vehicles: $properties['vehicles'] ?? [],
            species: $properties['species'] ?? [],
            createdAt: $properties['created'],
            updatedAt: $properties['edited'],
            url: $properties['url']
        );
    }
}