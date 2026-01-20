<?php

namespace App\Console\Commands;

use App\Repositories\RedisCacheRepository;
use App\Services\MetricsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RecomputeMetricsCommand extends Command
{
    private MetricsService $metricsService;

    public function __construct()
    {
        parent::__construct();
        $this->metricsService = new MetricsService(new RedisCacheRepository());
    }

    protected $signature = 'metrics:recompute';

    protected $description = 'Recompute metrics snapshot from stored events';

    public function handle(): int
    {

        $this->info('Recomputing metrics...');

        $raw = Redis::lrange('metrics:snapshot', 0, -1);


        $this->metricsService->recompute();

        $this->info('Metrics recomputed successfully.');

        return Command::SUCCESS;
    }
}
