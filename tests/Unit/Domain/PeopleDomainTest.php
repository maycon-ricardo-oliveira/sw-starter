<?php

namespace Tests\Unit\Domain;

use App\Domain\PeopleDomain;
use Tests\TestCase;

class PeopleDomainTest extends TestCase
{
    public function test_it_creates_people_domain_correctly()
    {
        $domain = new PeopleDomain(
            id: '1',
            name: 'Luke Skywalker',
            gender: 'male',
            skinColor: 'fair',
            hairColor: 'blond',
            eyeColor: 'blue',
            height: 172,
            mass: '77',
            birthYear: '19BBY',
            homeworld: 'https://swapi.tech/api/planets/1',
            movies: [],
            vehicles: [],
            starships: [],
            createdAt: '2025-01-01',
            updatedAt: '2025-01-01',
            url: 'https://www.swapi.tech/api/people/1'
        );

        $this->assertEquals('Luke Skywalker', $domain->getName());
        $this->assertEquals(172, $domain->getHeight());
        $this->assertFalse($domain->hasStarships());
    }

}