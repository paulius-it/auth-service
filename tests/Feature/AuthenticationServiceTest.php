<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    /**
     * Authentication service test.
     */
    public function test_error_when_trying_to_authenticate_no_providers(): void
    {
        $response = $this->post('/api/authenticate-provider', []);

        $response->assertStatus(400);
    }

    public function test_authenticate_lp_provider(): void
    {
        $response = $this->post('/api/authenticate-provider', [
            'providers' => [
                'lp_express' => true,
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_authenticate_omniva_provider(): void
    {
        $response = $this->post('/api/authenticate-provider', [
            'providers' => [
                'omniva' => true,
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_authentication_token_is_cached(): void
    {
        $response = $this->post('/api/authenticate-provider', [
            'providers' => [
                'lp_express' => true,
            ],
            'cache' => true,
        ]);

        $response->assertStatus(200);
        $response = $this->get(route('get-api-credentials', [
            'provider' => 'lp_express',
        ]));

        $response->assertJsonStructure([
            'lp_express_access_token',
        ]);
    }

    public function test_omniva_config_is_present(): void
    {
        $response = $this->post('/api/authenticate-provider', [
            'providers' => [
                'omniva' => true,
            ],
            'cache' => true,
        ]);

        $response->assertStatus(200);
        $response = $this->get(route('get-api-credentials', [
            'provider' => 'omniva',
        ]));

        $response->assertJsonStructure([
            'omniva_config',
        ]);
    }
}
