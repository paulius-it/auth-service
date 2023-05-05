<?php

namespace App\Api\Controllers;

use App\Api\Controllers\ApiController;
use App\Api\Services\AuthenticationService;
use App\Api\Services\TokenCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends ApiController
{
    public function __construct(
        private AuthenticationService $auth,
        private TokenCacheService $cache
    ) {
    }

    public function authenticate(Request $request): JsonResponse
    {
        $providers = $request->input('providers') ?? null;

        $cacheTokens = $request->boolean('cache');

        $authResult = $this->auth->authenticate(
            providers: $providers,
            cacheTokens: $cacheTokens);

            $jsonData = json_decode($authResult->content());

        $statusCode = $jsonData->status_code;

        return response()->json($authResult->content(), $statusCode);
    }

    /**
     * Gets API credentials to be used in all the requests to the specific API
     * 
     * @param string $provider
     * @returns array $result
     * 
     *Access_token for the LP Express API, and config data for the Omniva API
     */
    public function getApiCredentials(Request $request): JsonResponse
    {
        $provider = $request->input('provider') ?? null;

        $result = [];
        $result['status_code'] = 404;
        $result['message'] = 'No provider was given or it is not available';

        switch ($provider) {
            case 'lp_express':
                $accessToken = $this->cache->getLpExpressApiAccessToken();

                if ($accessToken) {
                    $result['lp_express_access_token'] = $accessToken;
                    $result['status_code'] = 200;
                    $result['message'] = 'Success';

                    if($request->input('refresh_token')) {
                        $result['lp_express_refresh_token'] = $this->cache->getLpExpressApiToken(refresh: true);
                    }
                } else {
                    $result['status_code'] = 404;
                    $result['message'] = 'LP token was not found!';
                }
                break;
            case 'omniva':
                $omnivaConfig = $this->auth->getApiConfig(provider: 'omniva');

                if (
                    $omnivaConfig['api_access_key']
                    && $omnivaConfig['api_secret']
                ) {
                    $result['omniva_config'] = $omnivaConfig;
                    $result['status_code'] = 200;
                    $result['message'] = 'Success';
                } else {
                    $result['status_code'] = 400;
                    $result['message'] = 'Omniva config is missing!';
                }
                break;
        }

        return response()->json($result, $result['status_code']);
    }
}