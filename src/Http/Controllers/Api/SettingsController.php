<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Lara\Jarvis\Services\SettingsService;

class SettingsController extends Controller
{
    protected $service;

    public function __construct (SettingsService $service)
    {
        $this->service = $service;
    }

    public function index (Request $request)
    {
        try {
            return response()->json($this->service->index($request));
        } catch (Exception $e) {
            if (method_exists($e, 'getStatusCode'))
                return $this->error($e->getMessage(), $e->getStatusCode());
            return $this->error($e->getMessage());
        }
    }

    public function update (Request $request)
    {
        try {
            return response()->json($this->service->update($request));
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (Exception $e) {
            if (method_exists($e, 'getStatusCode'))
                return $this->error($e->getMessage(), $e->getStatusCode());
            return $this->error($e->getMessage());
        }
    }

    public function audits (Request $request, $id)
    {
        try {
            return response()->json($this->service->audits($request, $id));
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            if (method_exists($e, 'getStatusCode'))
                return $this->error($e->getMessage(), $e->getStatusCode());
            return $this->error($e->getMessage());
        }
    }
}
