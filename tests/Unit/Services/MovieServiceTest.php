<?php

namespace Tests\Unit\Services;

use App\Adapters\SwapiAdapter;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Services\MovieService;
use Mockery;
use Tests\TestCase;

class MovieServiceTest extends TestCase
{



    public function test_movie_service_calls_repository_and_returns_dto()
    {
        $repo = Mockery::mock(SearchRepositoryInterface::class);

        $repo->shouldReceive('search')
            ->once()
            ->with('hope')
            ->andReturn([
                [
                    'uid' => '1',
                    'properties' => [
                        'title' => 'A New Hope',
                        'episode_id' => 4,
                        'director' => 'George Lucas',
                        'producer' => 'Gary Kurtz',
                        'release_date' => '1977-05-25',
                        'opening_crawl' => 'Test crawl',
                        'created' => '2025-01-01',
                        'edited' => '2025-01-01',
                        'url' => 'https://www.swapi.tech/api/films/1'
                    ]
                ]
            ]);

        $service = new MovieService($repo);

        $result = $service->search('hope');

        $this->assertIsArray($result);
    }


}