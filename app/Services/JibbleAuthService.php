<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class JibbleAuthService
{
    protected string $cacheKey = 'jibble.access_token';

    public function getToken(): string
    {
        if ($token = Cache::get($this->cacheKey)) {
            return $token;
        }

        $clientId = config('services.jibble.client_id');
        $clientSecret = config('services.jibble.client_secret');

        if (!$clientId || !$clientSecret) {
            throw new RuntimeException('Jibble credentials are missing.');
        }

        $response = Http::asForm()->post('https://identity.prod.jibble.io/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Unable to authenticate with Jibble: '.$response->body());
        }

        $data = $response->json();
        $token = $data['access_token'] ?? null;
        $expiresIn = max(60, (int) ($data['expires_in'] ?? 3600) - 300);

        if (!$token) {
            throw new RuntimeException('Jibble authentication response missing access_token.');
        }

        Cache::put($this->cacheKey, $token, $expiresIn);

        return $token;
    }
}
