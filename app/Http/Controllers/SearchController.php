<?php

namespace App\Http\Controllers;

use App\Adapters\SwapiAdapter;
use App\Enums\SearchTypeEnum;
use App\Http\Requests\SearchRequest;
use App\Repositories\MovieRepository;
use App\Repositories\PeopleRepository;
use App\Services\MovieService;
use App\Services\PeopleService;
use App\Services\SearchService;
use App\Utils\HttpCode;

class SearchController extends Controller
{
    protected SearchService $service;
    private SwapiAdapter $apiAdapter;

    public function __construct()
    {
        $this->apiAdapter = new SwapiAdapter();
        $this->service = new SearchService(
            new PeopleService(new PeopleRepository($this->apiAdapter)),
            new MovieService(new MovieRepository($this->apiAdapter))
        );
    }

    public function search(SearchRequest $request)
    {
        try {

            $data = $request->validated();
            $searchType = SearchTypeEnum::from($data['type']);
            $response = $this->service->search($searchType, $data['term']);
            return $this->sendResponse($response, "List of {$data['type']} search with term {$data['term']}");

        }catch (\Exception $e){
            return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST);
        }

    }

    public function details(string $type, string $id)
    {
        try {

            $searchType = SearchTypeEnum::from($type);
            $response = $this->service->details($searchType, $id);
            return $this->sendResponse($response, "List of {$type} search with term {$id}");

        }catch (\Exception $e){
            return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST);
        }
    }

}