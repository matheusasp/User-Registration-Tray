<?php

namespace App\Services;

use App\Models\User;
//use Google\Client\Google_Client;
use Google_Service_Oauth2;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    protected $client;

    /**
     * Constructor for GoogleAuthService.
     */
    public function __construct()
    {
        $this->client = new \Google\Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope('email');
        $this->client->addScope('profile');
        $this->client->addScope(Google_Service_Gmail::GMAIL_SEND);
    }

    /**
     * Get Google OAuth URL for authentication.
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle callback from Google OAuth.
     *
     * @param string $code
     * @return array
     */
    public function handleCallback(string $code): array
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($token);
            

            $service = new Google_Service_Oauth2($this->client);
            $userInfo = $service->userinfo->get();
            
      

            $user = User::updateOrCreate(
                ['email' => $userInfo->getEmail()],
                ['google_token' => json_encode($token)]
            );
            
            return [
                'success' => true,
                'user' => $user,
                'token' => $token
            ];
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user email using saved token.
     *
     * @param string $token
     * @return string|null
     */
    public function getUserEmail(string $token): ?string
    {
        try {
            $tokenData = json_decode($token, true);
            $this->client->setAccessToken($tokenData);
            
            $service = new Google_Service_Oauth2($this->client);
            $userInfo = $service->userinfo->get();
            
            return $userInfo->getEmail();
        } catch (\Exception $e) {
            Log::error('Failed to get user email: ' . $e->getMessage());
            return null;
        }
    }
}