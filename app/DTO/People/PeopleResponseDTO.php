<?php

namespace App\DTO\People;

use App\Domain\PeopleDomain;
use JsonSerializable;

class PeopleResponseDTO implements JsonSerializable
{
    public function __construct(
        private readonly PeopleDomain $people
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->people->getName(),
            'gender' => $this->people->getGender(),
            'skinColor' => $this->people->getSkinColor(),
            'hairColor' => $this->people->getHairColor(),
            'eyeColor' => $this->people->getEyeColor(),
            'height' => $this->people->getHeight(),
            'mass' => $this->people->getMass(),
            'birthYear' => $this->people->getBirthYear(),
            'homeworld' => $this->people->getHomeworld(),
            'films' => $this->people->getFilms(),
            'vehicles' => $this->people->getVehicles(),
            'starships' => $this->people->getStarships(),
            'createdAt' => $this->people->getCreatedAt(),
            'updatedAt' => $this->people->getUpdatedAt(),
            'url' => $this->people->getUrl(),
        ];
    }
}
