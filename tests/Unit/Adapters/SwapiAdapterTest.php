<?php

namespace Tests\Unit\Adapters;

use App\Adapters\SwapiAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;
use GuzzleHttp\Psr7\Request;

class SwapiAdapterTest extends TestCase
{
    private function makeAdapterWithMock(array $responses): SwapiAdapter
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        $client = new Client([
            'handler' => $handlerStack,
        ]);

        return new SwapiAdapter($client);
    }

    public function test_it_searches_resource_successfully()
    {
        $adapter = $this->makeAdapterWithMock([
            new Response(200, [], json_encode([
                'result' => [
                    ['uid' => '1', 'properties' => ['name' => 'Luke Skywalker']]
                ]
            ]))
        ]);

        $result = $adapter->search('people', ['name' => 'luke']);

        $this->assertCount(1, $result);
        $this->assertEquals('1', $result[0]['uid']);
        $this->assertEquals('Luke Skywalker', $result[0]['properties']['name']);
    }

    public function test_it_finds_resource_by_id()
    {
        $adapter = $this->makeAdapterWithMock([
            new Response(200, [], json_encode([
                'result' => [
                    'uid' => '10',
                    'properties' => [
                        'name' => 'Obi-Wan Kenobi'
                    ]
                ]
            ]))
        ]);

        $result = $adapter->find('people', '10');

        $this->assertEquals('10', $result['uid']);
        $this->assertEquals('Obi-Wan Kenobi', $result['properties']['name']);
    }

    public function test_it_throws_exception_on_invalid_status_code()
    {
        $adapter = $this->makeAdapterWithMock([
            new Response(500, [], json_encode(['error' => 'Server error']))
        ]);

        $this->expectException(\RuntimeException::class);

        $adapter->search('people', ['name' => 'luke']);
    }

    public function test_it_throws_exception_on_invalid_response_format()
    {
        $adapter = $this->makeAdapterWithMock([
            new Response(200, [], json_encode(['foo' => 'bar']))
        ]);

        $this->expectException(\RuntimeException::class);

        $adapter->search('people', ['name' => 'luke']);
    }

    public function it_throws_exception_on_connection_error()
    {
        $mock = new MockHandler([
            new ConnectException(
                'Connection failed',
                new Request('GET', 'people')
            )
        ]);

        $client = new Client([
            'handler' => HandlerStack::create($mock),
        ]);

        $adapter = new SwapiAdapter($client);

        $this->expectException(\RuntimeException::class);

        $adapter->search('people', ['name' => 'luke']);
    }

}
