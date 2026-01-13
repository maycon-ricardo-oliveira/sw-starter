<?php

namespace Tests\Unit\Services;

use App\Adapters\SwapiAdapter;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Services\PeopleService;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class PeopleServiceTest extends TestCase
{

    public function test_people_service_calls_repository_and_returns_dto()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $repo = Mockery::mock(SearchRepositoryInterface::class);

        $repo->shouldReceive('search')
            ->once()
            ->with('luke')
            ->andReturn([
                [
                    'uid' => '1',
                    'properties' => [
                        'name' => 'Luke Skywalker',
                        'gender' => 'male',
                        'height' => '172',
                        'mass' => '77',
                        'created' => '2025-01-01',
                        'edited' => '2025-01-01',
                        'homeworld' => 'https://www.swapi.tech/api/planets/1',
                        'url' => 'https://www.swapi.tech/api/people/1'
                    ]
                ]
            ]);

        $service = new PeopleService($repo);

        $result = $service->search('luke');

        $this->assertIsArray($result);
    }


}