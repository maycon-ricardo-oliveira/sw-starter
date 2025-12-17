<?php

namespace App\Services;

use App\Services\Contracts\SearchServiceInterface;
use JsonSerializable;

class MovieService implements SearchServiceInterface
{

    public function search(string $term)
    {
        // TODO: Implement search() method.
    }

    public function details(string $id): JsonSerializable
    {
        // TODO: Implement details() method.
    }
}