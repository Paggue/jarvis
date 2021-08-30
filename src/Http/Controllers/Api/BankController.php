<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Services\BankService as Service;
use Illuminate\Http\Request;

class BankController extends Controller
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the city resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if(!$request->order)
            $request->merge(['order' => "name,asc"]);

        return $this->service->index($request);
    }
}
