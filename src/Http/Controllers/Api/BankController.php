<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Http\Controllers\ControllerTrait;
use Lara\Jarvis\Services\BankService as Service;

class BankController extends Controller
{
    use ControllerTrait;

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }
}
