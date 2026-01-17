<?php

namespace App\Repositories\Contracts;

interface CacheRepositoryInterface
{
    public function get(string $key): ?array;

    public function set(string $key, array $value, int $ttlSeconds = 3600): void;

    public function flush(): void;

    public function remember(
        string $key,
        int $ttlSeconds,
        callable $callback
    );
}
