<?php

namespace App\Api\Interfaces;

interface Authenticatable
{
    public function authenticate(?string $apiName = null): ApiAuth;
}
