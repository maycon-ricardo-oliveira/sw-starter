<?php

namespace App\Repositories;

use App\Repositories\Contracts\MetricsRepositoryInterface;

class MetricsRepository implements MetricsRepositoryInterface
{
    private const SNAPSHOT_KEY = 'metrics:snapshot';
    private RedisCacheRepository $cache;

    public function __construct(
        RedisCacheRepository $cache
    ) {
        $this->cache = $cache;
    }

    public function append(string $key, array $event): void
    {
        // TODO: Implement append() method.
    }

    public function all(string $key): array
    {
        // TODO: Implement all() method.
    }

    public function clear(string $key): void
    {
        // TODO: Implement clear() method.
    }

    public function latest(string $key): ?array
    {
        // TODO: Implement latest() method.
    }

    public function save(string $key, array $data, int $ttlSeconds = 3600): void
    {
        // TODO: Implement save() method.
    }
}