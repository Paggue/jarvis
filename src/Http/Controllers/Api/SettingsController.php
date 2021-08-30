<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lara\Jarvis\Services\SettingsService;

class SettingsController extends Controller
{

    protected $service;

    public function __construct(SettingsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json( $this->service->index($request));
    }

    public function update(Request $request)
    {
        $data = $this->service->update($request);
        return response()->json($data);

    }

    public function audits (Request $request, $id)
    {
        try {
            $result = $this->service->audits($request, $id);
            return response()->json($result);

        } catch (ModelNotFoundException $m) {

            return $this->error("Not Found!", 404);

        } catch (Exception $e) {

            if (method_exists($e, 'getStatusCode'))
                return $this->error($e->getMessage(), $e->getStatusCode());

            return $this->error($e->getMessage());
        }
    }
}
