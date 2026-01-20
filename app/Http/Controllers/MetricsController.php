<?php

namespace App\Http\Controllers;

use App\Repositories\RedisCacheRepository;
use App\Services\MetricsService;

class MetricsController extends Controller
{
    private MetricsService $metricsService;

    public function __construct() {
        $this->metricsService = new MetricsService(new RedisCacheRepository());
    }

    public function index()
    {
        return response()->json(
            $this->metricsService->getMetrics()
        );
    }
}
