<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created order from the Enterprise Storefront.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'country_id' => 'required|exists:countries,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:25',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $pricingService = app(\App\Services\PricingService::class);
            $country = \App\Models\Country::find($validated['country_id']);
            
            $order = new Order();
            $order->fill($validated);
            $order->status = 'pending';
            $order->total_amount = 0;
            $order->margin_amount = 0;
            $order->sourcing_price = 0;
            $order->save();

            $totalRevenue = 0;
            $totalMargin = 0;
            $totalSourcing = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                // Calculate pricing for this line item via Strategic Engine
                $pricing = $pricingService->calculateFinalPrice(
                    (float) $product->sourcing_price,
                    (float) ($product->shipping_cost ?? 10.0), // Fallback shipping cost
                    $country->code
                );

                $order->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price_at_order' => $pricing['selling_price'],
                ]);

                $totalRevenue += $pricing['selling_price'] * $item['quantity'];
                $totalMargin += $pricing['margin_amount'] * $item['quantity'];
                $totalSourcing += $pricing['cost_subtotal'] * $item['quantity'];
                
                // Assign first product's vendor to the order for isolation
                if (!$order->vendor_id) {
                    $order->vendor_id = $product->vendor_id;
                }
            }

            $order->update([
                'total_amount' => $totalRevenue,
                'selling_price' => $totalRevenue,
                'margin_amount' => $totalMargin,
                'sourcing_price' => $totalSourcing,
            ]);

            return response()->json([
                'message' => 'Empire Transaction Initialized',
                'order_id' => $order->id,
                'total_amount' => $totalRevenue,
                'currency' => $country->currency_code,
            ], 201);
        });
    }
}
