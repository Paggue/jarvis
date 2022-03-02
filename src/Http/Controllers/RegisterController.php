<?php

namespace Jarvis\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RegisterController extends Controller
{
    public function create()
    {
        return view('custom-auth::register');
    }

    public function store(Request $request)
    {
        dd($request);
    }
}
