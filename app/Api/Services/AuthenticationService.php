<?php

namespace App\Api\Services;

use App\Api\Common\ProviderConstants;
use App\Api\Services\ApiAuth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AuthenticationService
{
    private Collection $errors; // For error handleing during API authentication
    private string $baseApiUrl;

    public function authenticate(?array $providers = null)
    {
        $this->errors = collect();
        if (!$providers) {
            $this->addError('No APIs to authenticate');
        }

        $lpProvider = $providers['LP'] ?? null;
        $omnivaProvider = $providers['OM'] ?? null;

        $lpConfig = [
            'api_access_key' => config("shipping.providers.lp_express.api_access_key", ''),
            'api_secret' => config("shipping.providers.lp_express.api_secret", ''),
        ];

        $omnivaConfig = [
            'api_access_key' => config("shipping.providers.omniva.api_access_key", ''),
            'api_secret' => config("shipping.providers.omniva.api_secret", ''),
        ];

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
            $apiResponse = HTTP::asForm()->post($this->baseApiUrl, $requestParams);
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
            'apiResponse' => $apiResponse->body(),
        ];

        return response()->json($response);
    }

    private function addError(string $error)
    {
        $this->errors->prepend($error);
    }
}
