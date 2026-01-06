<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the product prices.
     */
    public function index(string $productId): JsonResponse
    {
        $product = Product::with('baseCurrency')->findOrFail($productId);
        
        $prices = $product->prices()->with('currency')->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'base_price' => [
                    'price' => $product->price,
                    'currency' => $product->baseCurrency
                ],
                'prices' => $prices
            ]
        ]);
    }

    /**
     * Store a newly created product price in storage.
     */
    public function store(Request $request, string $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        
        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'price' => 'required|numeric|min:0|max:99999999.99',
        ]);
        
        $validated['product_id'] = $product->id;
        
        $productPrice = ProductPrice::create($validated);
        
        $productPrice->load('currency');
        
        return response()->json([
            'success' => true,
            'message' => 'Precio creado exitosamente',
            'data' => $productPrice
        ], 201);
    }
}
