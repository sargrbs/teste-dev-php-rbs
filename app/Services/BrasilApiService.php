<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class BrasilApiService
{
    protected $baseUrl = 'https://brasilapi.com.br/api';

    public function findCnpj(string $cnpj)
    {
        try {
            $response = Http::get("{$this->baseUrl}/cnpj/v1/{$cnpj}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (RequestException $e) {
            return null;
        }
    }
}
