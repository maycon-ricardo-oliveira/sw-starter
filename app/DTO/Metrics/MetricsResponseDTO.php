<?php

namespace App\DTO\Metrics;

class MetricsResponseDTO
{
    public function __construct(
        public array $totalSearchesByType,
        public array $topTerms,
        public array $dailySearches,
        public array $lastSearches,
        public float $averageRequestTimeMs,
        public string $updatedAt
    ) {}

    public static function fromSnapshot(array $snapshot): self
    {
        return new self(
            $snapshot['totalSearchesByType'] ?? ['people' => 0, 'movie' => 0],
            $snapshot['topTerms'] ?? ['people' => [], 'movie' => []],
            $snapshot['dailySearches'] ?? [],
            $snapshot['lastSearches'] ?? [],
            (float) ($snapshot['averageRequestTimeMs'] ?? 0),
            $snapshot['updatedAt'] ?? now()->toIso8601String()
        );
    }

    /**
     * Nunca quebra o front
     */
    public static function empty(): self
    {
        return new self(
            ['people' => 0, 'movie' => 0],
            ['people' => [], 'movie' => []],
            [],
            [],
            0,
            now()->toIso8601String()
        );
    }

    public function toArray(): array
    {
        return [
            'totalSearchesByType' => $this->totalSearchesByType,
            'topTerms' => $this->topTerms,
            'dailySearches' => $this->dailySearches,
            'lastSearches' => $this->lastSearches,
            'averageRequestTimeMs' => $this->averageRequestTimeMs,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
