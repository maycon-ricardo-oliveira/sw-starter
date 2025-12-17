<?php

namespace App\Repositories\Contracts;

interface SearchRepositoryInterface
{
    public function search(string $term);

    public function find(string $id): array;
}
