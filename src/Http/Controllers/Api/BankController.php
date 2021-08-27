<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Http\Resources\DefaultCollection;
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
     * @return \Illuminate\Http\JsonResponse|DefaultCollection
     */
    public function index(Request $request)
    {
        if(!$request->order)
            $request->merge(['order' => "name,asc"]);

        return $this->service->index($request);
    }
}
