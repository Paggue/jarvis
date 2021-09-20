<?php

namespace Lara\Jarvis\Http\Controllers\Api;

use Lara\Jarvis\Http\Controllers\Controller;
use Lara\Jarvis\Http\Controllers\ControllerTrait;
use Lara\Jarvis\Services\SettingsService;

class SettingsController extends Controller
{
    use ControllerTrait;

    protected $service;

    public function __construct (SettingsService $service)
    {
        $this->service = $service;
    }
}
