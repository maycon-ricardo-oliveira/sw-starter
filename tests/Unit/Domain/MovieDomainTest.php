<?php

namespace Tests\Unit\Domain;

use App\Domain\MovieDomain;
use Tests\TestCase;

class MovieDomainTest extends TestCase
{
    public function test_it_creates_movie_domain_with_valid_data()
    {
        $domain = new MovieDomain(
            id: '1',
            title: 'A New Hope',
            episodeId: 4,
            director: 'George Lucas',
            producer: 'Gary Kurtz',
            releaseDate: '1977-05-25',
            openingCrawl: 'It is a period of civil war...',
            characters: [],
            planets: [],
            starships: [],
            vehicles: [],
            species: [],
            createdAt: '2025-01-01',
            updatedAt: '2025-01-01',
            url: 'https://www.swapi.tech/api/films/1'
        );

        $this->assertEquals(4, $domain->getEpisodeId());
        $this->assertEquals('A New Hope', $domain->getTitle());
    }

}