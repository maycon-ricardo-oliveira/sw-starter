<?php

namespace App\Services\Contracts;

use JsonSerializable;

interface SearchServiceInterface
{
    public function search(string $term);

    public function details(string $id);
}
