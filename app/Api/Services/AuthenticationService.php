<?php

namespace App\Api\Services;

use App\Api\Common\ProviderConstants;
use App\Api\Interfaces\Authenticatable;
use App\Api\Interfaces\ConfigurationInterface;
use App\Api\Services\TokenCacheService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AuthenticationService implements Authenticatable, ConfigurationInterface
{
    private Collection $errors; // For error handleing during API authentication
    private string $baseApiUrl;

    public function __construct(private TokenCacheService $tokenCache)
    {
    }

    public function authenticate(?array $providers = null): JsonResponse
    {
        $this->errors = collect();
        if (!$providers) {
            $this->addError('No APIs to authenticate');
        }

        $lpProvider = $providers['lp_express'] ?? null;
        $omnivaProvider = $providers['omniva'] ?? null;

        $lpConfig = $this->getApiConfig(provider: 'lp_express');

        $omnivaConfig = $this->getApiConfig(provider: 'omniva');

        if ($lpConfig['api_access_key'] && $lpConfig['api_secret']) {
            $this->baseApiUrl = ProviderConstants::BASE_LP_EXPRESS_API_URL;
            $requestParams = [
                'scope' => ProviderConstants::LP_EXPRESS_API_SCOPE,
                'grant_type' => ProviderConstants::LP_EXPRESS_API_GRANT_TYPE,
                'clientSystem' => ProviderConstants::LP_EXPRESS_API_CLIENT_SYSTEM,
                'username' => $lpConfig['api_access_key'],
                'password' => $lpConfig['api_secret'],
            ];
        }

        try {
            $lpApiResponse = HTTP::asForm()->post($this->baseApiUrl, $requestParams);
        } catch (\Exception $e) {
            $this->addError('Authentication failed: ' . $e->getMessage());
        }

        if ($this->errors->count() > 0) {
            $response = [
                'status_code' => 401,
                'error' => $this->errors?->first(),
            ];

            return response()->json($response);
        }

        $response = [
            'status_code' => 200,
        ];

        if ($lpApiResponse) {
            $response['lp_api_response'] = $lpApiResponse->body();
        }

        $tokensCached = $this->cacheLpApiTokens($lpApiResponse);

        $response['tokens_cached'] = $tokensCached;

        return response()->json($response);
    }

    public function getApiConfig(?string $provider = null): array
    {
        switch ($provider) {
            case 'lp_express':
                $config = [
                    'api_access_key' => config("shipping.providers.lp_express.api_access_key", ''),
                    'api_secret' => config("shipping.providers.lp_express.api_secret", ''),
                ];
                break;
            case 'omniva':
                $config = [
                    'api_access_key' => config("shipping.providers.omniva.api_access_key", ''),
                    'api_secret' => config("shipping.providers.omniva.api_secret", ''),
                ];
                break;
            default:
                $config = []; // No other option
        }

        return $config;
    }




    private function addError(string $error)
    {
        $this->errors->prepend($error);
    }

    private function cacheLpApiTokens(?Response $lpApiData = null): bool
    {
        $lpAccessToken = $lpApiData->json('access_token') ?? null;
        $lpRefreshToken = $lpApiData->json('refresh_token') ?? null;
        $lpTokenExpiresIn = $lpApiData->json('expires_in') ?? null;

        $tokenData = [
            'lp_express' => [
                'name' => 'lp_express',
                'access_token' => $lpAccessToken,
                'refresh_token' => $lpRefreshToken,
                'expires_in' => $lpTokenExpiresIn,
            ],
        ];
        return $this->tokenCache->cacheApiTokens($tokenData);
    }
}
