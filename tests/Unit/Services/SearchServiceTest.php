<?php

namespace Tests\Unit\Services;

use App\Enums\SearchTypeEnum;
use App\Repositories\Contracts\SearchRepositoryInterface;
use App\Services\MetricsService;
use App\Services\MovieService;
use App\Services\PeopleService;
use App\Services\SearchService;
use Mockery;
use Tests\TestCase;

class SearchServiceTest extends TestCase
{

    public function test_search_calls_people_service()
    {
        $peopleService = Mockery::mock(PeopleService::class);
        $movieService = Mockery::mock(MovieService::class);
        $metricsService = Mockery::mock(MetricsService::class);

        $metricsService->shouldReceive('recordEvent')
            ->once()
            ->withArgs(function ($event, $type, $payload, $durationMs) {
                return $event === 'search'
                    && $type === 'people'
                    && $payload['term'] === 'luke'
                    && is_float($durationMs);
            });


        $peopleService
            ->shouldReceive('search')
            ->once()
            ->with('luke')
            ->andReturn([]);

        $service = new SearchService($peopleService, $movieService, $metricsService);

        $service->search(SearchTypeEnum::PEOPLE, 'luke');
    }

    public function test_search_calls_movie_service()
    {
        $peopleService = Mockery::mock(PeopleService::class);
        $movieService = Mockery::mock(MovieService::class);
        $metricsService = Mockery::mock(MetricsService::class);

        $metricsService->shouldReceive('recordEvent')
            ->once()
            ->withArgs(function ($event, $type, $payload, $durationMs) {
                return $event === 'search'
                    && $type === 'movie'
                    && $payload['term'] === 'hope'
                    && is_float($durationMs);
            });

        $movieService
            ->shouldReceive('search')
            ->once()
            ->with('hope')
            ->andReturn([]);

        $service = new SearchService($peopleService, $movieService, $metricsService);

        $service->search(SearchTypeEnum::MOVIE, 'hope');
    }



}