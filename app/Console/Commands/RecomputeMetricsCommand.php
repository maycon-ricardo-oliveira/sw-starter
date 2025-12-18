<?php

namespace App\Console\Commands;

use App\Jobs\RecomputeMetricsJob;
use Illuminate\Console\Command;

class RecomputeMetricsCommand extends Command
{
    protected $signature = 'metrics:recompute';
    protected $description = 'Dispatch metrics recompute job';

    public function handle(): void
    {
        RecomputeMetricsJob::dispatch()->onQueue('metrics');

        $this->info('Metrics recompute job dispatched.');
    }
}
