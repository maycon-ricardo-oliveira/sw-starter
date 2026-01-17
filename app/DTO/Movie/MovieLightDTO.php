<?php

namespace App\DTO\Movie;

class MovieLightDTO
{

    public function __construct(
        public string $id,
        public string $title,
        public string $url
    ) {}

    public static function make(array $item): self
    {
        return new self(
            id: $item['uid'],
            title: $item['properties']['title'],
            url: "/details/movie/{$item['uid']}",
        );
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'url'   => $this->url,
        ];
    }

}