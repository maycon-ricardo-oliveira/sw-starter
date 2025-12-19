<?php

namespace App\Domain;

use App\Enums\People\PeopleGenderEnum;
use App\Exceptions\PeopleException;

class PeopleDomain
{
    private string $id;
    private string $name;
    private string $gender;
    private string $skinColor;
    private string $hairColor;
    private string $eyeColor;
    private ?int $height;
    private ?string $mass;
    private ?string $birthYear;
    private string $homeworld;
    private array $movies;
    private array $vehicles;
    private array $starships;
    private string $createdAt;
    private string $updatedAt;
    private string $url;

    public function __construct(
        string $id,
        string $name,
        string $gender,
        string $skinColor,
        string $hairColor,
        string $eyeColor,
        ?int $height,
        ?string $mass,
        ?string $birthYear,
        string $homeworld,
        array $movies,
        array $vehicles,
        array $starships,
        string $createdAt,
        string $updatedAt,
        string $url
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->gender = $gender;
        $this->skinColor = $skinColor;
        $this->hairColor = $hairColor;
        $this->eyeColor = $eyeColor;
        $this->height = $height;
        $this->mass = $mass;
        $this->birthYear = $birthYear;
        $this->homeworld = $homeworld;
        $this->movies = $movies;
        $this->vehicles = $vehicles;
        $this->starships = $starships;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->url = $url;

        $this->validate();
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */
    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getGender(): string { return $this->gender; }
    public function getSkinColor(): string { return $this->skinColor; }
    public function getHairColor(): string { return $this->hairColor; }
    public function getEyeColor(): string { return $this->eyeColor; }
    public function getHeight(): ?int { return $this->height; }
    public function getMass(): ?string { return $this->mass; }
    public function getBirthYear(): ?string { return $this->birthYear; }
    public function getHomeworld(): string { return $this->homeworld; }
    public function getMovies(): array { return $this->movies; }
    public function getVehicles(): array { return $this->vehicles; }
    public function getStarships(): array { return $this->starships; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }
    public function getUrl(): string { return $this->url; }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    public function setHeight(?int $height): void
    {
        if ($height !== null && $height <= 0) {
            throw new PeopleException('Height must be greater than zero');
        }

        $this->height = $height;
    }

    public function setMass(?string $mass): void
    {
        $this->mass = $mass;
    }

    private function validate(): void
    {
        if (empty($this->name)) {
            throw new PeopleException('Name is required');
        }

        if (!in_array($this->gender, PeopleGenderEnum::values())) {
            throw new PeopleException('Invalid gender');
        }
    }

    public function hasMovies(): bool
    {
        return !empty($this->movies);
    }

    public function hasVehicles(): bool
    {
        return !empty($this->vehicles);
    }

    public function hasStarships(): bool
    {
        return !empty($this->starships);
    }
}
