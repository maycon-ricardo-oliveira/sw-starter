<?php

namespace App\DTO\People;

class PeopleLightDTO
{

    public function __construct(
        public string $id,
        public string $name,
        public string $url
    ) {}

    public static function make(array $item): self
    {
        return new self(
            id: $item['uid'],
            name: $item    ['name'],
            url: "/details/people/{$item['uid']}",
        );
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name' => $this->name,
            'url'   => $this->url,
        ];
    }

}