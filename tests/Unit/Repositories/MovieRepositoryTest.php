<?php

namespace Tests\Unit\Repositories;

use App\Adapters\SwapiAdapter;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Repositories\MovieRepository;
use App\Services\MovieService;
use Mockery;
use Tests\TestCase;

class MovieRepositoryTest extends TestCase
{

    private SwapiAdapter $adapter;
    private MovieRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = Mockery::mock(SwapiAdapter::class);
        $this->repository = new MovieRepository($this->adapter);
    }

    public function test_it_searches_movies_by_term()
    {
        $term = 'hope';

        $apiResponse = [
            [
                'uid' => '1',
                'properties' => [
                    'title' => 'A New Hope'
                ]
            ]
        ];

        $this->adapter
            ->shouldReceive('search')
            ->once()
            ->with('films', ['title' => 'hope'])
            ->andReturn([
                [
                    'uid' => '1',
                    'properties' => [
                        'title' => 'A New Hope',
                        'episode_id' => 4,
                        'director' => 'George Lucas',
                        'producer' => 'Gary Kurtz',
                        'release_date' => '1977-05-25',
                        'opening_crawl' => '...',
                        'characters' => [],
                        'planets' => [],
                        'starships' => [],
                        'vehicles' => [],
                        'species' => [],
                    ],
                ]
            ]);

        $result = $this->repository->search($term);

        $this->assertCount(1, $result);
        $this->assertEquals('A New Hope', $result[0]['properties']['title']);
    }

    public function test_it_finds_movie_by_id()
    {
        $id = '1';

        $apiResponse = [
            'uid' => '1',
            'properties' => [
                'title' => 'A New Hope'
            ]
        ];

        $this->adapter
            ->shouldReceive('find')
            ->once()
            ->with('films', $id)
            ->andReturn($apiResponse);

        $result = $this->repository->find($id);

        $this->assertEquals('1', $result['uid']);
        $this->assertEquals('A New Hope', $result['properties']['title']);
    }
}