<?php

namespace App\Adapters;

use App\Utils\HttpCode;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


class SwapiAdapter
{
    private Client $client;

    public function __construct()
    {
        $baseUrl = env('SWAPI_BASE_URL');

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout'  => 10,
        ]);

    }

    public function search(string $resource, array $term): array
    {
        try {
            $response = $this->client->get($resource, [
                'query' => $term,
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() !== HttpCode::SUCCESS) {
                throw new \Exception('SWAPI search failed');
            }

            $apiResponse = json_decode(
                $response->getBody()->getContents(),
                true
            );

            return $apiResponse["result"];

        } catch (GuzzleException $exception) {
            throw new \Exception(
                'Error communicating with SWAPI: ' . $exception->getMessage()
            );
        }
    }

    public function find(string $resource, string $id): array
    {
        try {

            $response = $this->client->get("{$resource}/{$id}");

            if ($response->getStatusCode() !== HttpCode::SUCCESS) {
                throw new \Exception('SWAPI search failed');
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['result'])) {
                throw new \RuntimeException('Resource not found');
            }

            return $data['result'];

        } catch (GuzzleException $exception) {
            throw new \Exception(
                'Error communicating with SWAPI: ' . $exception->getMessage()
            );
        }
    }
}