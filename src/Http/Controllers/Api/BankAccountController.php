<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Services\BankAccountService;

class BankAccountController extends Controller
{
    protected BankAccountService $service;

    public function __construct (BankAccountService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index (Request $request)
    {
        try {
            return $this->service->setModelType($request->model_type)->setId($request->model_id)->index($request);

        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store (Request $request)
    {
        try {
            $data = $this->service->setModelType($request->model_type)->setId($request->model_id)->store($request);
            return response()->json($data, 201);

        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show (Request $request, int $id)
    {
        try {
            return response()->json($this->service->setModelType($request->model_type)->setId($request->model_id)->show($request, $id));

        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update (Request $request, int $id)
    {
        try {
            $data = $this->service->setModelType($request->model_type)->setId($request->model_id)->update($request, $id);
            return response()->json($data);

        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            if (method_exists($e, 'getStatusCode'))
                return $this->error($e->getMessage(), $e->getStatusCode());
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function setMain (Request $request, int $id)
    {
        try {
            $data = $this->service->setModelType($request->model_type)->setId($request->model_id)->setMain($request, $id);
            return response()->json($data);

        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            if (method_exists($e, 'getStatusCode'))
                return $this->error($e->getMessage(), $e->getStatusCode());
            return $this->error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy (Request $request, int $id)
    {
        try {
            $this->service->setModelType($request->model_type)->setId($request->model_id)->destroy($request, $id);
            return response()->json(null, 204);

        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Restore the specified resource in the storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function restore (Request $request, int $id)
    {
        try {
            $data = $this->service->setModelType($request->model_type)->setId($request->model_id)->restore($request, $id);
            return response()->json($data);

        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
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
            $result = $this->service->setModelType($request->model_type)->setId($request->model_id)->audits($request, $id);
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
