<?php

namespace App\Repositories;

use App\Repositories\Contracts\CacheRepositoryInterface;
use App\Repositories\Contracts\MetricsRepositoryInterface;
use Illuminate\Support\Facades\Redis;

class RedisCacheRepository implements CacheRepositoryInterface, MetricsRepositoryInterface
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

    public function append(string $key, array $event): void
    {
        Redis::rpush($key, json_encode($event));
    }

    public function all(string $key): array
    {
        $raw = Redis::lrange($key, 0, -1);

        return array_map(
            fn ($e) => json_decode($e, true),
            $raw
        );
    }

    public function clear(string $key): void
    {
        Redis::del($key);
    }

    public function latest(string $key): ?array
    {
        return $this->get($key);
    }

    public function save(string $key, array $data, int $ttlSeconds = 3600): void
    {
        $this->set($key, $data, $ttlSeconds);
    }

}
