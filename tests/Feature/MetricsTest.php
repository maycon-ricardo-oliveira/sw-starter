<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class MetricsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Redis::flushall();

        Redis::rpush('metrics:events', json_encode([
            'event' => 'search',
            'type' => 'people',
            'payload' => ['term' => 'luke'],
            'duration_ms' => 100,
            'created_at' => '2026-01-20T15:00:00+00:00',
        ]));

        Redis::rpush('metrics:events', json_encode([
            'event' => 'search',
            'type' => 'movie',
            'payload' => ['term' => 'hope'],
            'duration_ms' => 200,
            'created_at' => '2026-01-20T15:05:00+00:00',
        ]));
    }

     public function test_it_recomputes_and_returns_metrics_when_snapshot_does_not_exist()
    {
        $response = $this->getJson('/api/metrics');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'totalSearchesByType' => [
                'people',
                'movie',
            ],
            'topTerms' => [
                'people',
                'movie',
            ],
            'dailySearches',
            'lastSearches',
            'averageRequestTimeMs',
            'updatedAt',
        ]);

        $data = $response->json();

        $this->assertIsInt($data['totalSearchesByType']['people']);
        $this->assertIsInt($data['totalSearchesByType']['movie']);

        $this->assertGreaterThanOrEqual(0, $data['totalSearchesByType']['people']);
        $this->assertGreaterThanOrEqual(0, $data['totalSearchesByType']['movie']);

        $this->assertIsArray($data['topTerms']['people']);
        $this->assertIsArray($data['topTerms']['movie']);

        $this->assertIsArray($data['dailySearches']);
        $this->assertIsArray($data['lastSearches']);

        $this->assertIsInt($data['averageRequestTimeMs']);
        $this->assertGreaterThanOrEqual(0, $data['averageRequestTimeMs']);

        $this->assertNotEmpty($data['updatedAt']);
    }
}
