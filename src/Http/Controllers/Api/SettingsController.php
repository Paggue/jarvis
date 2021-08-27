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
        $this->middleware("permission:settings:list")->only(["index"]);
        $this->middleware("permission:settings:edit")->only("update");
        $this->middleware("permission:settings:audits")->only("audits");

        $this->service = $service;
    }

    /**
     * Display a listing of the city resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json( $this->service->index($request));
    }

    /**
     * Display a listing of the settings resource.
     *
     * @param Request $request
     * @return \stdClass
     */
    public function update(Request $request)
    {
        $data = $this->service->update($request);
        return response()->json($data);

    }

    /**
     * Retrieves the audits of the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
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
