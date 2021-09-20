<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Http\Resources\DefaultCollection;
use Lara\Jarvis\Services\CitiesStatesService;
use Illuminate\Http\Request;

class CitiesStatesController extends Controller
{
    protected $service;

    public function __construct(CitiesStatesService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the city resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|DefaultCollection
     */
    public function indexCities(Request $request)
    {
        try {
            return $this->service->indexCities($request);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Display a listing of the state  resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|DefaultCollection
     */
    public function indexStates(Request $request)
    {
        try {
            return $this->service->indexStates($request);

        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
