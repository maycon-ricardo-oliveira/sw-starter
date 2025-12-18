<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ComputeRequestMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:compute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recompute request metrics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = Redis::lrange('metrics:request:times', 0, -1);

        if (empty($items)) {
            return;
        }

        $total = 0;
        $count = 0;

        foreach ($items as $item) {
            $data = json_decode($item, true);
            $total += $data['duration'];
            $count++;
        }

        $average = (int) ($total / $count);

        Redis::set('metrics:request:avg', json_encode([
            'average_ms' => $average,
            'computed_at' => now()->toISOString(),
        ]));

        // opcional: limpar dados brutos
        Redis::del('metrics:request:times');
    }
}
