<?php

namespace App\Services;

use App\Domain\MovieDomain;
use App\DTO\Movie\MovieResponseDTO;
use App\DTO\People\PeopleLightDTO;
use App\Repositories\Contracts\CacheRepositoryInterface;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Services\Contracts\SearchServiceInterface;
use Illuminate\Support\Facades\Cache;

class MovieService extends BaseService implements SearchServiceInterface
{

    private const SEARCH_TTL = 600;   // 10 minutes
    private const DETAIL_TTL = 3600;  // 1 hour
    private SearchRepositoryInterface $movieRepo;
    private CacheRepositoryInterface $cache;

    public function __construct(
        SearchRepositoryInterface $movieRepo,
        CacheRepositoryInterface $cache
    ) {
        $this->movieRepo = $movieRepo;
        $this->cache = $cache;
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

    public function details(string $id)
    {
        $cacheKey = "movie:detail:$id";

        return $this->cache->remember(
            $cacheKey,
            self::DETAIL_TTL,
            function () use ($id) {
                $data = $this->movieRepo->find($id);
                $related = $this->getCharactersByIds($data);

                return $this->convertToDTO($data, $related);
            }
        );
    }

    protected function convertToDTO($movie, $related = null): MovieResponseDTO
    {
        $movieDomain = $this->convertRecordToDomain($movie, $related);
        return new MovieResponseDTO($movieDomain);
    }

    protected function convertRecordToDomain($movie, $related = null): MovieDomain
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
            characters: $related ?? [],
            planets: $properties['planets'] ?? [],
            starships: $properties['starships'] ?? [],
            vehicles: $properties['vehicles'] ?? [],
            species: $properties['species'] ?? [],
            createdAt: $properties['created'],
            updatedAt: $properties['edited'],
            url: $properties['url']
        );
    }

    private function getCharactersByIds($data): array
    {

        $characterUrls = $data['properties']['characters'] ?? [];
        $characterIds  = $this->extractIds($characterUrls);

        if (empty($characterIds)) {
            return [];
        }

        $response = $this->movieRepo->findRelated($characterIds);

        return array_map(
            fn ($character) => PeopleLightDTO::make($character)->toArray(),
            $response
        );
    }

}