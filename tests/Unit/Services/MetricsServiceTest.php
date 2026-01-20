<?php
use App\Services\MetricsService;
use App\Repositories\Contracts\MetricsRepositoryInterface;
use Mockery;
use Tests\TestCase;

class MetricsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_returns_snapshot_when_it_exists()
    {
        $repo = Mockery::mock(MetricsRepositoryInterface::class);

        $repo->shouldReceive('latest')
            ->once()
            ->with('metrics:snapshot')
            ->andReturn([
                'totalSearchesByType' => ['people' => 1, 'movie' => 0],
                'topTerms' => ['people' => [], 'movie' => []],
                'dailySearches' => [],
                'lastSearches' => [],
                'averageRequestTimeMs' => 0,
                'updatedAt' => now()->toIso8601String(),
            ]);

        // 🔑 IMPORTANTE
        $repo->shouldReceive('save')->zeroOrMoreTimes()->withAnyArgs();

        $service = new MetricsService($repo);

        $metrics = $service->getMetrics();

        $this->assertEquals(1, $metrics->totalSearchesByType['people']);
    }

    public function test_it_recomputes_and_saves_snapshot_when_none_exists()
    {
        $repo = Mockery::mock(MetricsRepositoryInterface::class);

        $repo->shouldReceive('latest')
            ->once()
            ->with('metrics:snapshot')
            ->andReturn(null);

        $repo->shouldReceive('all')
            ->once()
            ->with('metrics:events')
            ->andReturn([
                json_encode([
                    'event' => 'search',
                    'type' => 'people',
                    'payload' => ['term' => 'luke'],
                    'duration_ms' => 100,
                    'created_at' => '2026-01-20T14:00:00+00:00',
                ])
            ]);

        $repo->shouldReceive('save')
            ->once()
            ->with(
                'metrics:snapshot',
                Mockery::on(fn ($metrics) =>
                    $metrics['totalSearchesByType']['people'] === 1
                ),
                Mockery::type('int')
            );

        $service = new MetricsService($repo);

        $metrics = $service->getMetrics();

        $this->assertEquals(1, $metrics->totalSearchesByType['people']);
    }

    public function test_it_returns_empty_metrics_when_no_events_exist()
    {
        $repo = Mockery::mock(MetricsRepositoryInterface::class);

        $repo->shouldReceive('latest')
            ->once()
            ->with('metrics:snapshot')
            ->andReturn(null);

        $repo->shouldReceive('all')
            ->once()
            ->with('metrics:events')
            ->andReturn([]);

        // 🔑 SEM ISSO, O TESTE QUEBRA
        $repo->shouldReceive('save')->once()->withAnyArgs();

        $service = new MetricsService($repo);

        $metrics = $service->getMetrics();

        $this->assertEquals(0, $metrics->averageRequestTimeMs);
    }
}