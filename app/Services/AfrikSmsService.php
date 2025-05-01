<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AfrikSmsService
{
    protected $clientId;
    protected $apiKey;
    protected $senderId;

    public function __construct()
    {
        $this->clientId = env('AFRIK_SMS_CLIENT_ID');
        $this->apiKey = env('AFRIK_SMS_API_KEY');
        $this->senderId = env('AFRIK_SMS_SENDER_ID', 'MRepublique'); 
    }

    public function sendSms($numero, $message)
    {
        $response = Http::asForm()->post('https://api.afriksms.com/api/web/web_v1/outbounds/send', [
            'ClientId' => $this->clientId,
            'ApiKey' => $this->apiKey,
            'SenderId' => $this->senderId,
            'Message' => $message,
            'MobileNumbers' => $numero,
        ]);

        return $response->json();
    }
}
