<?php

namespace Tests\Feature;

use Tests\TestCase;

class MetricsTest  extends TestCase
{

    public function test_metrics_endpoint_returns_success()
    {
        $response = $this->getJson('/api/metrics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

}