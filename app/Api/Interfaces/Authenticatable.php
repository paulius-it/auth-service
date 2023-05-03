<?php

namespace App\Api\Interfaces;

use Illuminate\Http\JsonResponse;

interface Authenticatable
{
    public function authenticate(?array $providers = null): JsonResponse;
}