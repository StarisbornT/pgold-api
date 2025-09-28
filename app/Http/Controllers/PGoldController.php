<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PGoldController extends Controller
{
    protected string $baseUrl = 'https://sandbox.pgoldapp.com/api/guest/';

    /**
     * Get available gift cards from PGold API
     */
    public function getGiftCards()
    {
        try {
            $response = Http::get($this->baseUrl . 'giftcards');

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch giftcards',
                'error' => $response->body(),
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available cryptocurrencies from PGold API
     */
    public function getCryptos()
    {
        try {
            $response = Http::get($this->baseUrl . 'cryptocurrencies');

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch cryptocurrencies',
                'error' => $response->body(),
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}