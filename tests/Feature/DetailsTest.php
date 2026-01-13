<?php

namespace Tests\Feature;

use Tests\TestCase;

class DetailsTest extends TestCase
{
    public function test_details_people_returns_success()
    {
        $response = $this->getJson('/api/details/people/10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'gender',
                    'height',
                    'mass',
                    'hairColor',
                    'skinColor',
                    'eyeColor',
                    'birthYear',
                    'createdAt'
                ]
            ]);
    }

    public function test_movie_details_returns_success()
    {
        $response = $this->getJson('/api/details/movie/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    public function test_details_invalid_type_returns_422()
    {
        $response = $this->getJson('/api/details/invalid/1');

        $response->assertStatus(422);

    }

}
