<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FontteService
{
    protected $token;
    protected $baseUrl = 'https://api.fonnte.com';

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        Log::info('Token Fontte:', ['token' => $this->token]); // Debug token
    }

    public function sendSMS($target, $message)
    {
        try {
            Log::info('Attempting to send SMS:', [
                'target' => $target,
                'message' => $message
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post($this->baseUrl . '/send', [
                        'target' => $target,
                        'message' => $message,
                    ]);

            Log::info('Fontte Response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Fontte SMS Error: ' . $e->getMessage());
            Log::error('Error details:', [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}