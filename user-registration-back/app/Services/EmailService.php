<?php

namespace App\Services;

use App\Models\User;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;

class EmailService
{
    protected $client;

    /**
     * Constructor for EmailService.
     */
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->addScope(Google_Service_Gmail::GMAIL_SEND);
    }

    /**
     * Send registration completion email.
     *
     * @param User $user
     * @return bool
     */
    public function sendRegistrationEmail(User $user): bool
    {
        try {
            $tokenData = json_decode($user->google_token, true);
            if (!$tokenData) {
                Log::error('Invalid Google token for user: ' . $user->id);
                return false;
            }

            $this->client->setAccessToken($tokenData);
            
            if ($this->client->isAccessTokenExpired()) {
                if (isset($tokenData['refresh_token'])) {
                    $this->client->fetchAccessTokenWithRefreshToken($tokenData['refresh_token']);
                    $user->update(['google_token' => json_encode($this->client->getAccessToken())]);
                } else {
                    Log::error('No refresh token available for user: ' . $user->id);
                    return false;
                }
            }
            
            $service = new Google_Service_Gmail($this->client);
            
            $subject = "Registration Completed Successfully";
            $messageText = "Hello " . ($user->name ?? "there") . ",\n\n";
            $messageText .= "Thank you for registering on our system.\n";
            $messageText .= "Your account has been successfully created.\n\n";
            $messageText .= "Regards,\n";
            $messageText .= "The Team";
            
            $rawMessage = "To: " . $user->email . "\r\n";
            $rawMessage .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
            $rawMessage .= "MIME-Version: 1.0\r\n";
            $rawMessage .= "Content-Type: text/plain; charset=utf-8\r\n";
            $rawMessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $rawMessage .= $messageText;
            
            $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
            $message = new Google_Service_Gmail_Message();
            $message->setRaw($encodedMessage);
            
            $service->users_messages->send("me", $message);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }
}