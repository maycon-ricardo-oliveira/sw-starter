<?php

namespace App\DTO\Movie;

use App\Domain\MovieDomain;
use JsonSerializable;

class MovieResponseDTO implements JsonSerializable
{
    public function __construct(
        private MovieDomain $movie
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->movie->getId(),
            'title' => $this->movie->getTitle(),
            'episodeId' => $this->movie->getEpisodeId(),
            'director' => $this->movie->getDirector(),
            'producer' => $this->movie->getProducer(),
            'releaseDate' => $this->movie->getReleaseDate(),
            'openingCrawl' => $this->movie->getOpeningCrawl(),

            'characters' => $this->movie->getCharacters(),
            'planets' => $this->movie->getPlanets(),
            'starships' => $this->movie->getStarships(),
            'vehicles' => $this->movie->getVehicles(),
            'species' => $this->movie->getSpecies(),

            'createdAt' => $this->movie->getCreatedAt(),
            'updatedAt' => $this->movie->getUpdatedAt(),
            'url' => $this->movie->getUrl(),
        ];
    }
}
