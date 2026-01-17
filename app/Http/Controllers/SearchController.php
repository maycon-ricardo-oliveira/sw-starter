<?php

namespace App\Http\Controllers;

use App\Adapters\SwapiAdapter;
use App\Enums\SearchTypeEnum;
use App\Http\Requests\DetailsRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\MovieRepository;
use App\Repositories\PeopleRepository;
use App\Repositories\RedisCacheRepository;
use App\Services\MetricsService;
use App\Services\MovieService;
use App\Services\PeopleService;
use App\Services\SearchService;
use App\Utils\HttpCode;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    protected SearchService $service;
    private SwapiAdapter $apiAdapter;

    public function __construct()
    {
        $this->apiAdapter = new SwapiAdapter();
        $this->service = new SearchService(
            new PeopleService(new PeopleRepository($this->apiAdapter), new RedisCacheRepository()),
            new MovieService(new MovieRepository($this->apiAdapter),  new RedisCacheRepository()),
            new MetricsService()
        );
    }

    public function search(SearchRequest $request)
    {
        try {

            $data = $request->validated();
            $searchType = SearchTypeEnum::from($data['type']);

            $response = $this->service->search($searchType, $data['term']);
            return $this->sendResponse($response, "List of {$data['type']} search with term {$data['term']}");

        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST);
        }

    }

    public function details(DetailsRequest $request): JsonResponse
    {
        try {

            $data = $request->validated();
            $searchType = SearchTypeEnum::from($data['type']);
            $response = $this->service->details($searchType, $data['id']);

            return $this->sendResponse(
                $response,
                "Details of {$data['type']} with id {$data['id']}"
            );

        } catch (\Exception $e) {
            return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST);
        }
    }

}