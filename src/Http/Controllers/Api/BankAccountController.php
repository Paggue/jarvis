<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Http\Controllers\ControllerTrait;
use Lara\Jarvis\Services\BankAccountService;
use Exception;

class BankAccountController extends Controller
{
    use ControllerTrait;

    protected $service;

    public function __construct(BankAccountService $service)
    {
        $this->service = $service;
    }

    public function setMain(Request $request, int $id)
    {
        try {
            $data = $this->service->setModelType($request->model_type)->setId($request->model_id)->setMain(
                $request,
                $id
            );
            return response()->json($data);
        } catch (ValidationException $v) {
            return $this->error($v->errors(), $v->status);
        } catch (ModelNotFoundException $m) {
            return $this->error("Not Found!", 404);
        } catch (Exception $e) {
            if (method_exists($e, 'getStatusCode')) {
                return $this->error($e->getMessage(), $e->getStatusCode());
            }
            return $this->error($e->getMessage());
        }
    }
}
