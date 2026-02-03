<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


try {
    echo "--- SETUP ---\n";
    // 1. Setup Data
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'price' => 100]);
    echo "Product Created: {$product->name} (Stock: {$product->stock})\n";

    echo "\n--- TEST 1: STORE ORDER ---\n";
    // 2. Test Store (Reduce Stock)
    $controller = new \App\Http\Controllers\OrderController();
    $request = \Illuminate\Http\Request::create('/api/orders', 'POST', [
        'customer_id' => $customer->id,
        'order_date' => now()->toDateTimeString(),
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2]
        ]
    ]);
    
    $response = $controller->store($request);
    $order = $response->getData();
    
    $product->refresh();
    echo "Order Created: ID {$order->id}, Total: {$order->total_amount}\n";
    echo "New Stock: {$product->stock} (Expected: 8)\n";

    if ($product->stock !== 8) throw new Exception("Stock deduction failed!");

    echo "\n--- TEST 2: UPDATE ORDER (CHANGE QTY) ---\n";
    // 3. Test Update (Restore then Deduct new)
    // Change quantity from 2 to 5. Logic: Restore 2 (Stock=10), Deduct 5 (Stock=5).
    $updateRequest = \Illuminate\Http\Request::create("/api/orders/{$order->id}", 'PUT', [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 5]
        ]
    ]);
    
    $controller->update($updateRequest, Order::find($order->id));
    $product->refresh();
    echo "Order Updated (Qty 2 -> 5)\n";
    echo "New Stock: {$product->stock} (Expected: 5)\n";

    if ($product->stock !== 5) throw new Exception("Stock update failed!");

    echo "\n--- TEST 3: DESTROY ORDER ---\n";
    // 4. Test Destroy (Restore Stock)
    $controller->destroy(Order::find($order->id));
    $product->refresh();
    echo "Order Deleted\n";
    echo "Final Stock: {$product->stock} (Expected: 10)\n";

    if ($product->stock !== 10) throw new Exception("Stock restoration failed!");

    echo "\n--- TEST 4: INSUFFICIENT STOCK ---\n";
    try {
        $failRequest = \Illuminate\Http\Request::create('/api/orders', 'POST', [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateTimeString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 100] // More than 10
            ]
        ]);
        $controller->store($failRequest);
        echo "FAILED: Validation check missed!\n";
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "PASSED: Caught insufficient stock error.\n";
    }

    echo "\nALL TESTS PASSED.\n";

} catch (\Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
