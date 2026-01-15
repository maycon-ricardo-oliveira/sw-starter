<?php

namespace Tests\Unit\Services;

use App\Services\MetricsService;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class MetricsServiceTest extends TestCase
{
    protected MetricsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MetricsService();
    }

    public function test_it_records_a_metrics_event()
    {
        Redis::shouldReceive('rpush')
            ->once()
            ->withArgs(function ($key, $payload) {
                $this->assertEquals('metrics:events', $key);

                $event = json_decode($payload, true);

                $this->assertEquals('search', $event['event']);
                $this->assertEquals('people', $event['type']);
                $this->assertEquals(['term' => 'luke'], $event['payload']);
                $this->assertEquals(123.45, $event['duration_ms']);
                $this->assertArrayHasKey('created_at', $event);

                return true;
            });

        $this->service->recordEvent(
            event: 'search',
            type: 'people',
            payload: ['term' => 'luke'],
            durationMs: 123.45
        );
    }

    public function test_it_recomputes_metrics_and_stores_snapshot()
    {
        $events = [
            json_encode([
                'event' => 'search',
                'type' => 'people',
                'payload' => ['term' => 'luke'],
                'duration_ms' => 100,
                'created_at' => now()->subMinutes(2)->toIso8601String(),
            ]),
            json_encode([
                'event' => 'search',
                'type' => 'movie',
                'payload' => ['term' => 'hope'],
                'duration_ms' => 200,
                'created_at' => now()->subMinute()->toIso8601String(),
            ]),
        ];

        Redis::shouldReceive('lrange')
            ->once()
            ->with('metrics:events', 0, -1)
            ->andReturn($events);

        Redis::shouldReceive('set')
            ->once()
            ->withArgs(function ($key, $payload) {
                $this->assertEquals('metrics:snapshot', $key);

                $data = json_decode($payload, true);

                $this->assertArrayHasKey('totalSearchesByType', $data);
                $this->assertEquals(1, $data['totalSearchesByType']['people']);
                $this->assertEquals(1, $data['totalSearchesByType']['movie']);

                $this->assertArrayHasKey('topTerms', $data);
                $this->assertArrayHasKey('averageRequestTimeMs', $data);
                $this->assertArrayHasKey('updatedAt', $data);

                return true;
            });

        $metrics = $this->service->recompute();

        $this->assertEquals(1, $metrics['totalSearchesByType']['people']);
        $this->assertEquals(1, $metrics['totalSearchesByType']['movie']);
        $this->assertEquals(150, $metrics['averageRequestTimeMs']);
    }
}
