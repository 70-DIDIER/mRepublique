<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayGateService
{
    protected $authToken;
    protected $baseUrl;

    public function __construct()
    {
        $this->authToken = env('PAYGATE_AUTH_TOKEN'); // On va le mettre dans .env
        $this->baseUrl = 'https://paygateglobal.com/api/v1'; // Base URL
    }

    /**
     * Initier un paiement Flooz ou TMoney
     */
    public function demanderPaiement($phoneNumber, $amount, $description, $identifier, $network)
    {
        $response = Http::timeout(180)->post($this->baseUrl . '/pay', [
            'auth_token' => $this->authToken,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'description' => $description,
            'identifier' => $identifier,
            'network' => strtoupper($network), // FLOOZ ou TMONEY
        ]);

        return $response->json();
    }

    /**
     * Vérifier l'état d'une transaction
     */
    public function verifierPaiement($txReference)
    {
        $response = Http::timeout(180)->post($this->baseUrl . '/status', [
            'auth_token' => $this->authToken,
            'tx_reference' => $txReference,
        ]);

        return $response->json();
    }

    /**
     * Vérifier l'état d'une transaction par l'identifier interne
     */
    public function verifierPaiementParIdentifier($identifier)
    {
        $response = Http::post('https://paygateglobal.com/api/v2/status', [
            'auth_token' => $this->authToken,
            'identifier' => $identifier,
        ]);

        return $response->json();
    }
}
