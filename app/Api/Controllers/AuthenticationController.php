<?php

namespace App\Api\Controllers;

use App\Api\Controllers\ApiController;
use App\Api\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends ApiController
{
    public function __construct(
        private AuthenticationService $auth
    ) {
    }

    public function authenticate(Request $request): JsonResponse
    {
        $providers = $request->input('providers') ?? null;

        $authResult = $this->auth->authenticate($providers);

        return response()->json($authResult->content());
    }
}
