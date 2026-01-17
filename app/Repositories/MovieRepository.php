<?php

namespace App\Repositories;

use App\Adapters\SwapiAdapter;
use App\Repositories\Contracts\SearchRepositoryInterface;

class MovieRepository implements SearchRepositoryInterface
{

    const RESOURCE = 'films';
    const PEOPLE_RESOURCE = 'people';

    public function __construct(private SwapiAdapter $adapter) { }

    public function search(string $term): array
    {
        return $this->adapter->search(self::RESOURCE, ['title' => $term]);
    }

    public function find(string $id): array
    {
        return $this->adapter->find(self::RESOURCE, $id);
    }

    public function findRelated(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return $this->adapter->search(
            self::PEOPLE_RESOURCE,
            ['ids' => implode(',', $ids)]
        );
    }
}