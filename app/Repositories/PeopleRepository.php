<?php

namespace App\Repositories;

use App\Adapters\SwapiAdapter;
use App\Repositories\Contracts\SearchRepositoryInterface;


class PeopleRepository implements SearchRepositoryInterface
{

    const RESOURCE ='people';
    const MOVIE_RESOURCE = 'films';

    public function __construct(private SwapiAdapter $adapter) { }

    public function search(string $term): array
    {
        return $this->adapter->search(self::RESOURCE, ['name' => $term]);
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
            self::MOVIE_RESOURCE,
            ['ids' => implode(',', $ids)]
        );
    }
}
