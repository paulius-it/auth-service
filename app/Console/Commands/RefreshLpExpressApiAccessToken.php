<?php

namespace App\Console\Commands;

use App\Api\Services\AuthenticationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshLpExpressApiAccessToken extends Command
{
    protected $signature = 'cron:refreshLPExpressAccessToken';

    protected $description = 'Refreshes LPExpress API access token';

    public function handle(
        AuthenticationService $authentication
    ): void {
        $key = 'app.apiProviders.lp_express';

        $accessToken = Cache::get($key . 'access_token');
        $expiresIn = Cache::get('app.lp_token_expires_in');
        $refreshToken = Cache::get($key . 'refresh_token');

        if ($accessToken && $expiresIn && $refreshToken) {
            $expiresIn = Carbon::parse($expiresIn);
            $now = Carbon::now();
            if ($expiresAt->diffInSeconds($now) > 10) {
                $this->info('LPExpress API access token is still valid');
                return;
            }
        }

        // Token is expired or doesn't exist, so we need to request a new token. But before this, let's clear the expiration time from the cache
        Cache::forget('app.lp_token_expires_in');

        $response = $this->auth->authenticate(
            providers: ['lp_express'],
            cacheTokens: true,
            needsRefresh: true,
            refreshToken: $refreshToken
        );

        $responseBody = $response->json();

        if ($response->ok() && $responseBody['tokens_cached']) {
            $this->info('LPExpress API access token has been refreshed');
        } else {
            $this->error('Failed to refresh LPExpress API access token');
        }
    }
}
