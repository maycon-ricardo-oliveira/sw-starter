<?php

namespace App\Repositories;

use App\Repositories\Contracts\CacheRepositoryInterface;
use Illuminate\Support\Facades\Redis;

class RedisCacheRepository implements CacheRepositoryInterface
{
    public function get(string $key): ?array
    {
        $value = Redis::get($key);

        return $value ? json_decode($value, true) : null;
    }

    public function set(string $key, $value, int $ttlSeconds = 3600): void
    {
        Redis::setex(
            $key,
            $ttlSeconds,
            json_encode($value)
        );
    }

    public function flush(): void
    {
        Redis::flushall();
    }

    public function remember(
        string $key,
        int $ttlSeconds,
        callable $callback
    ) {
        $cached = $this->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();

        $this->set($key, $value, $ttlSeconds);

        return $value;
    }
}
