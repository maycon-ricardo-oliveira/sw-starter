<?php

namespace App\Http\Controllers;

use App\Services\MetricsService;

class MetricsController extends Controller
{

    public function __construct(
        private MetricsService $metricsService
    ) { }

    public function index()
    {
        return response()->json(
            $this->metricsService->getMetrics()
        );
    }

}