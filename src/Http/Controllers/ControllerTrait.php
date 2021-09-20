<?php

namespace Lara\Jarvis\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Services\ServiceTrait;

trait ControllerTrait
{
    protected $service;

    public function __construct (ServiceTrait $service)
    {
        $this->service = $service;
    }

    public function index (Request $request)
    {
        try {
            return $this->service->index($request);
        } catch (Exception $e) {
            if (method_exists($e, 'getStatusCode'))
                return $this->error($e->getMessage(), $e->getStatusCode());
            return $this->error($e->getMessage());
        }
    }

    public function indexAll (Request $request)
    {
        try {
            return $this->service->indexAll($request);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store (Request $request)
    {
        try {
            $data = $this->service->store($request);
            return response()->json($data, 201);
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show (Request $request, $id = null)
    {
        try {
            return response()->json($this->service->show($request, $id));
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update (Request $request, $id = null)
    {
        try {
            $data = $this->service->update($request, $id);
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

    public function destroy (Request $request, $id = null)
    {
        try {
            $this->service->destroy($request, $id);
            return response()->json(null, 204);
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function restore (Request $request, $id = null)
    {
        try {
            $data = $this->service->restore($request, $id);
            return response()->json($data);
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function audits (Request $request, $id = null)
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
