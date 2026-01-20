<?php

namespace App\Repositories\Contracts;

interface MetricsRepositoryInterface
{
    public function append(string $key, array $event): void;

    public function all(string $key): array;

    public function clear(string $key): void;

    public function latest(string $key): ?array;

    public function save(string $key, array $data, int $ttlSeconds = 3600): void;
}