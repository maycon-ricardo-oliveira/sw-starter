<?php

namespace App\Repositories;

use App\Adapters\SwapiAdapter;
use App\Repositories\Contracts\SearchRepositoryInterface;

class MovieRepository implements SearchRepositoryInterface
{

    const RESOURCE = 'films';

    public function __construct(private SwapiAdapter $adapter) { }

    public function search(string $term): array
    {
        return $this->adapter->search(self::RESOURCE, ['title' => $term]);
    }

    public function find(string $id): array
    {
        return $this->adapter->find(self::RESOURCE, $id);
    }
}