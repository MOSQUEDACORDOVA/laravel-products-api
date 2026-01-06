<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the currencies.
     */
    public function index(): JsonResponse
    {
        $currencies = Currency::all()->makeVisible('exchange_rate');
        
        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }
}
