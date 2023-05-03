<?php

namespace App\Api\Interfaces;

/**
 * Interface for API config keys
 */

interface ConfigurationInterface
{
    public function getApiConfig(?string $provider = null): array;
}
