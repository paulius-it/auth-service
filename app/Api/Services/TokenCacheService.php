<?php

namespace App\Api\Services;

use App\Api\Services\AuthenticationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Used to cache and retrieve API tokens
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
                    Cache::put('app.apiProviders.' . $provider . '.access_token', $info['access_token'], $info['expires_in']);
                    Cache::put('app.apiProviders.' . $provider . '.refresh_token', $info['refresh_token'], $info['expires_in']);
                    Cache::put('app.lp_token_expires_in', Carbon::now()->addSeconds($info['expires_in']));
                    break;
            } // Not required for omniva, so skipping
        }

        $lpCacheResult = Cache::has('app.apiProviders.' . $provider . '.access_token')
            && Cache::has('app.apiProviders.' . $provider . '.refresh_token');

        return $lpCacheResult;
    }

    /**
     * Gets API token from cache
     * 
     * @param bool $refresh: indicates a refresh token is needed to get a new access token
     * 
     * @return string
     */
    public function getLpExpressApiAccessToken(bool $refresh = false): ?string
    {
        $key = 'app.apiProviders.lp_express.access_token';

        if ($refresh) {
            $key = 'app.apiProviders.lp_express.refresh_token';
        }

        if (!Cache::has($key)) {
            return null;
        }

        $token = Cache::get($key);

        if ($token) {
            if (!$refresh) {
                return $token;
            } else {
                return $token;
            }
        }
        return null;
    }
}
