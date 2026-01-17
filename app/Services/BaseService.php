<?php

namespace App\Services;

class BaseService
{
    protected function extractIds(array $urls): array
    {
        return array_map(
            fn ($url) => basename(rtrim($url, '/')),
            $urls
        );
    }
}