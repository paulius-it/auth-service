<?php

namespace App\Api\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Used to cache received API tokens
 * Note: only applicable to LP Express API!
 */
class TokenCacheService
{

    public function cacheApiTokens(array $tokenData): bool
    {
        foreach ($tokenData as $provider => $info) {
            $provider = $info['name'];

            switch ($provider) {
                case 'lp_express':
                    Cache::put('app.apiProviders.' . $provider . 'access_token', $info['access_token'], $info['expires_in']);
                    Cache::put('app.apiProviders.' . $provider . 'refresh_token', $info['refresh_token'], $info['expires_in']);
                    break;
            }
        }

        $lpCacheResult = Cache::has('app.apiProviders.' . $provider . 'access_token')
            && Cache::has('app.apiProviders.' . $provider . 'refresh_token');

        return $lpCacheResult;
    }

    /**
     * Gets API token from cache
     */
    public function getApiAccessToken(string $provider): ?string
    {
        $key = 'app.apiProviders.' . $provider . 'access_token';

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        return null;
    }
}
