<?php

namespace App\Domain;

class MovieDomain
{
    private string $id;
    private string $title;
    private int $episodeId;
    private string $director;
    private string $producer;
    private string $releaseDate;
    private string $openingCrawl;

    private array $characters;
    private array $planets;
    private array $starships;
    private array $vehicles;
    private array $species;

    private string $createdAt;
    private string $updatedAt;
    private string $url;

    public function __construct(
        string $id,
        string $title,
        int $episodeId,
        string $director,
        string $producer,
        string $releaseDate,
        string $openingCrawl,
        array $characters,
        array $planets,
        array $starships,
        array $vehicles,
        array $species,
        string $createdAt,
        string $updatedAt,
        string $url
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->episodeId = $episodeId;
        $this->director = $director;
        $this->producer = $producer;
        $this->releaseDate = $releaseDate;
        $this->openingCrawl = $openingCrawl;
        $this->characters = $characters;
        $this->planets = $planets;
        $this->starships = $starships;
        $this->vehicles = $vehicles;
        $this->species = $species;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->url = $url;
    }

    // Getters

    public function getId() { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getEpisodeId(): int { return $this->episodeId; }
    public function getDirector(): string { return $this->director; }
    public function getProducer(): string { return $this->producer; }
    public function getReleaseDate(): string { return $this->releaseDate; }
    public function getOpeningCrawl(): string { return $this->openingCrawl; }

    public function getCharacters(): array { return $this->characters; }
    public function getPlanets(): array { return $this->planets; }
    public function getStarships(): array { return $this->starships; }
    public function getVehicles(): array { return $this->vehicles; }
    public function getSpecies(): array { return $this->species; }

    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }
    public function getUrl(): string { return $this->url; }
}
