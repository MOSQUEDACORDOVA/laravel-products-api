<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Http\Requests\StoreProductPriceRequest;
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
    public function store(StoreProductPriceRequest $request, string $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        
        $productPrice = ProductPrice::updateOrCreate(
            [
                'product_id' => $product->id,
                'currency_id' => $request->validated()['currency_id']
            ],
            [
                'price' => $request->validated()['price']
            ]
        );
        
        $productPrice->load('currency');
        
        $wasRecentlyCreated = $productPrice->wasRecentlyCreated;
        
        return response()->json([
            'success' => true,
            'message' => $wasRecentlyCreated ? 'Precio creado exitosamente' : 'Precio actualizado exitosamente',
            'data' => $productPrice
        ], $wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Remove the specified product price from storage.
     */
    public function destroy(string $productId, ProductPrice $price): JsonResponse
    {
        $product = Product::findOrFail($productId);

        if ($price->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Recurso no encontrado',
                'error' => 'El precio no pertenece a este producto'
            ], 404);
        }

        $price->delete();

        return response()->json([
            'success' => true,
            'message' => 'Precio eliminado exitosamente'
        ]);
    }
}
