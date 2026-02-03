<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStockTest extends TestCase
{
    // use RefreshDatabase; // Commented out to avoid wiping existing DB if user wants to keep data, but simpler for consistent tests. 
    // Actually, for a proper test script we should use RefreshDatabase, but the user is running a dev server.
    // I will use a dedicated test user/product to avoid messing up main data too much, or rely on transaction rollbacks if I could.
    // Given the context, I'll use RefreshDatabase but be aware it wipes DB. 
    // Wait, the user has data they might want to keep? The prompt implies a test techinal, so standard testing practices apply.
    use RefreshDatabase;

    public function test_order_creation_deducts_stock()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 100,
            'stock' => 10,
            'category' => 'Electronics'
        ]);

        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890'
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertEquals(8, $product->fresh()->stock);
    }

    public function test_cannot_order_more_than_stock()
    {
        $product = Product::create([
            'name' => 'Low Stock Product',
            'sku' => 'TEST-002',
            'price' => 100,
            'stock' => 5,
            'category' => 'Electronics'
        ]);

        $customer = Customer::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0987654321'
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 6]
            ]
        ]);

        $response->assertStatus(422); // Validation error
        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_order_update_recalculates_stock()
    {
        $product = Product::create([
            'name' => 'Update Product',
            'sku' => 'TEST-003',
            'price' => 100,
            'stock' => 10,
            'category' => 'Electronics'
        ]);

        $customer = Customer::create(['name' => 'Test', 'email' => 't@t.com', 'phone' => '123']);

        // Create initial order with 2 items. Stock should be 8.
        $orderResponse = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

        $orderId = $orderResponse->json('id');

        // Update order to 5 items. 
        // Logic: Restore 2 (Stock=10). Deduct 5 (Stock=5).
        $this->putJson("/api/orders/{$orderId}", [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5]
            ]
        ])->assertStatus(200);

        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_order_deletion_restores_stock()
    {
        $product = Product::create([
            'name' => 'Delete Product',
            'sku' => 'TEST-004',
            'price' => 100,
            'stock' => 10,
            'category' => 'Electronics'
        ]);

        $customer = Customer::create(['name' => 'Test', 'email' => 't@t.com', 'phone' => '123']);

        // Create order with 3 items. Stock -> 7.
        $orderResponse = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3]
            ]
        ]);

        $orderId = $orderResponse->json('id');
        $this->assertEquals(7, $product->fresh()->stock);

        // Delete order. Stock -> 10.
        $this->deleteJson("/api/orders/{$orderId}")->assertStatus(200);

        $this->assertEquals(10, $product->fresh()->stock);
    }
}
