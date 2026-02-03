<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::with('customer')->latest('order_date')->get();
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $order->load(['customer', 'orderItems.product']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $totalAmount = 0;

            // Create the order first
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'total_amount' => 0, // Will update later
                'status' => 'Pendiente',
            ]);

            foreach ($validated['items'] as $item) {
                // Lock the product for update to prevent race conditions
                $product = Product::lockForUpdate()->find($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for product: {$product->name}. Requested: {$item['quantity']}, Available: {$product->stock}",
                    ]);
                }

                // Decrement stock
                $product->decrement('stock', $item['quantity']);

                $subtotal = $product->price * $item['quantity'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $order->update(['total_amount' => $totalAmount]);

            return response()->json($order->load('orderItems'), 201);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'order_date' => 'sometimes|date',
            'status' => 'sometimes|in:Pendiente,Pagado,Cancelado',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated, $order, $request) {
            // Update basic fields if present
            $order->update($request->only(['customer_id', 'order_date', 'status']));

            if (isset($validated['items'])) {
                // 1. Restore stock for ALL existing items
                foreach ($order->orderItems as $existingItem) {
                    $product = Product::lockForUpdate()->find($existingItem->product_id);
                    $product->increment('stock', $existingItem->quantity);
                }

                // 2. Delete existing items
                $order->orderItems()->delete();

                // 3. Process new items
                $totalAmount = 0;

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => "Insufficient stock for product: {$product->name}. Requested: {$item['quantity']}, Available: {$product->stock}",
                        ]);
                    }

                    // Decrement stock
                    $product->decrement('stock', $item['quantity']);

                    $subtotal = $product->price * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ]);

                    $totalAmount += $subtotal;
                }

                $order->update(['total_amount' => $totalAmount]);
            }

            return response()->json($order->fresh('orderItems'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        return DB::transaction(function () use ($order) {
            // Restore stock for all items
            foreach ($order->orderItems as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            $order->delete();

            return response()->json(['message' => 'Order cancelled and stock restored'], 200);
        });
    }
}
