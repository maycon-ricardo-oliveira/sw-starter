<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchTest extends TestCase
{
    public function test_search_people_returns_success_response(): void
    {
        $response = $this->getJson('/api/search/people?term=luke');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'gender',
                    'birthYear',
                ]
            ]
        ]);
    }

    public function test_search_movies_returns_success()
    {
        $response = $this->getJson('/api/search/movie?term=hope');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    public function test_search_without_term_returns_validation_error(): void
    {
        $response = $this->getJson('/api/search/people');


        $response->assertStatus(422);

        $response->assertJson(fn ($json) =>
            $json->has('formErrors.term')
            );

        $response->assertJsonStructure([
            'formErrors' => ['term']
        ]);
    }


    public function test_search_with_invalid_type_returns_422(): void
    {
        $response = $this->getJson('/api/search/invalid?term=test');

        $response->assertStatus(422);
    }


}
