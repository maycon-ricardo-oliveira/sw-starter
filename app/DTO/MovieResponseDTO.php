<?php

namespace App\DTO;

use JsonSerializable;

class MovieResponseDTO implements JsonSerializable
{
    public function __construct(
        private string $id,
        private string $title,
        private int $episodeId,
        private string $releaseDate,
        private string $director,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'episode_id' => $this->episodeId,
            'release_date' => $this->releaseDate,
            'director' => $this->director,
        ];
    }
}
