<?php

namespace Lara\Jarvis\Http\Controllers\Auth;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    protected $service;

    public function __construct(TwoFactorService $service)
    {
        $this->service = $service;
    }

    public function getUrlCode(Request $request)
    {
        try {
            $data = $this->service->getUrlCode($request);
            return $data;
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (QueryException $q) {
            return $this->error($q->getMessage(), 500);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function enable(Request $request)
    {
        try {
            $data = $this->service->enable($request);
            return $data;
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (QueryException $q) {
            return $this->error($q->getMessage(), 500);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function disable(Request $request)
    {
        try {
            $data = $this->service->disable($request);
            return $data;
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (QueryException $q) {
            return $this->error($q->getMessage(), 500);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function check(Request $request)
    {
        try {
            $data = $this->service->check($request);
            return $data;
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (QueryException $q) {
            return $this->error($q->getMessage(), 500);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
