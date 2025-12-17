<?php

namespace App\Http\Controllers;

use App\Adapters\SwapiAdapter;
use App\Http\Requests\SearchRequest;
use App\Repositories\PeopleRepository;
use App\Services\SearchService;
use App\Utils\HttpCode;

class SearchController extends Controller
{
    protected $service;
    private SwapiAdapter $apiAdapter;

    public function __construct()
    {
        $this->apiAdapter = new SwapiAdapter();
        $this->service = new SearchService(new PeopleRepository($this->apiAdapter));
    }

    public function search(SearchRequest $request)
    {
        try {

            $data = $request->validated();
            $response = $this->service->search($data['type'], $data['term']);

            return $this->sendResponse($response, "List of {$data['type']} search with term {$data['term']}");


        }catch (\Exception $e){
            return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST);
        }

    }

    public function details(string $type, string $id)
    {
        try {

            $response = $this->service->details($type, $id);
            return $this->sendResponse($response, "List of {$type} search with term {$id}");

        }catch (\Exception $e){
            return $this->sendResponse([], $e->getMessage(), HttpCode::BAD_REQUEST);
        }
    }

}