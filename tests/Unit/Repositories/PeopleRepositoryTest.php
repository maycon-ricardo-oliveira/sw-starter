<?php

namespace Tests\Unit\Repositories;

use App\Adapters\SwapiAdapter;
use App\Repositories\PeopleRepository;
use Mockery;
use Tests\TestCase;

class PeopleRepositoryTest extends TestCase
{
    private SwapiAdapter $adapter;
    private PeopleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = Mockery::mock(SwapiAdapter::class);
        $this->repository = new PeopleRepository($this->adapter);
    }

    public function test_it_searches_people_by_term()
    {
        $term = 'luke';

        $apiResponse = [
            [
                'uid' => '1',
                'properties' => [
                    'name' => 'Luke Skywalker'
                ]
            ]
        ];

        $this->adapter
            ->shouldReceive('search')
            ->once()
            ->with('people', ['name' => $term])
            ->andReturn($apiResponse);

        $result = $this->repository->search($term);

        $this->assertCount(1, $result);
        $this->assertEquals('Luke Skywalker', $result[0]['properties']['name']);
    }

    public function test_it_finds_people_by_id()
    {
        $id = '1';

        $apiResponse = [
            'uid' => '1',
            'properties' => [
                'name' => 'Luke Skywalker'
            ]
        ];

        $this->adapter
            ->shouldReceive('find')
            ->once()
            ->with("people", $id)
            ->andReturn($apiResponse);

        $result = $this->repository->find($id);

        $this->assertEquals('1', $result['uid']);
        $this->assertEquals('Luke Skywalker', $result['properties']['name']);
    }
}
