<?php

namespace App\Api\Services;

use Illuminate\Http\JsonResponse;

class ApiAuth
{
    public function __construct(
        private string $provider,
        private string $response
    ) {
    }

    public function generateAuthResponse(): JsonResponse
    {
        $authResult = [];
        $authResult['provider'] = $this->provider;
        if ($this->provider == 'omniva') {
            $apiData = [
                'access_token' => '',
                'refresh_token' => '',
            ];
        } else if ($this->provider == 'lp') {
            $apiData = [
                'access_token' => '',
                'refresh_token' => '',
            ];
        }

        $authResult['api_data'] = json_encode($apiData);
        $authResult['status_msg'] = 'success or fail';

        return response()->json($authResult);
    }
}
