<?php

namespace App\Adapters;

use App\Utils\HttpCode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;

class SwapiAdapter
{
    private Client $client;

    private const TIMEOUT = 5;
    private const RETRY_TIMES = 3;
    private const RETRY_DELAY_MS = 200;

    public function __construct(?Client $client = null)
    {
        // If a client is injected (tests), do not override configuration
        if ($client) {
            $this->client = $client;
            return;
        }

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::retry(
                $this->retryDecider(),
                $this->retryDelay()
            )
        );

        $this->client = new Client([
            'base_uri'        => env('SWAPI_BASE_URL'),
            'handler'         => $stack,
            'timeout'         => self::TIMEOUT,
            'connect_timeout' => self::TIMEOUT,
            'http_errors'     => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function search(string $resource, array $term): array
    {
        try {
            $response = $this->client->get($resource, [
                'query' => $term,
            ]);

            return $this->handleResponse($response);

        } catch (GuzzleException $e) {
            throw new \RuntimeException(
                'Failed to communicate with SWAPI (search)',
                previous: $e
            );
        }
    }

    public function find(string $resource, string $id): array
    {
        try {
            $response = $this->client->get("{$resource}/{$id}");

            return $this->handleResponse($response, single: true);

        } catch (GuzzleException $e) {
            throw new \RuntimeException(
                'Failed to communicate with SWAPI (find)',
                previous: $e
            );
        }
    }

    private function handleResponse(ResponseInterface $response, bool $single = false): array
    {
        if ($response->getStatusCode() !== HttpCode::SUCCESS) {
            throw new \RuntimeException(
                'SWAPI returned error status: ' . $response->getStatusCode()
            );
        }

        $data = json_decode(
            $response->getBody()->getContents(),
            true
        );

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON returned by SWAPI');
        }

        if (!isset($data['result'])) {
            throw new \RuntimeException('Invalid SWAPI response format');
        }

        if ($single && empty($data['result'])) {
            throw new \RuntimeException('Resource not found');
        }

        return $data['result'];
    }

    private function retryDecider(): callable
    {
        return function (
            int $retries,
                $request,
                $response = null,
            GuzzleException $exception = null
        ) {
            if ($retries >= self::RETRY_TIMES) {
                return false;
            }

            if ($exception instanceof GuzzleException) {
                return true;
            }

            if ($response && $response->getStatusCode() >= 500) {
                return true;
            }

            // Optional: rate limit
            if ($response && $response->getStatusCode() === 429) {
                return true;
            }

            return false;
        };
    }

    private function retryDelay(): callable
    {
        return fn (int $retries) => self::RETRY_DELAY_MS * $retries;
    }
}
