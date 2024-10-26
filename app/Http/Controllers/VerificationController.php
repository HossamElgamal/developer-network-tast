<?php

namespace App\Http\Controllers;

use App\services\AuthService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function verify(Request $request)
    {
        return $this->authService->verifyCode($request);
    }
}
