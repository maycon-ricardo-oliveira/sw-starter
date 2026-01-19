<?php

namespace App\Services\Contracts;

interface SearchServiceInterface
{
    public function search(string $term);

    public function details(string $id);
}
