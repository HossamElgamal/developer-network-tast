<?php

namespace App\Http\Controllers;

use App\services\AuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function login(Request $request)
    {
        return $this->authService->login($request->all());
    }
}
