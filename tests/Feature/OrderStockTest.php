<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
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
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);
    }

    public function test_cannot_order_more_than_stock()
    {
        $product = Product::factory()->create(['stock' => 5]);
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'status' => 'Pendiente',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 6]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items']);
        
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_order_update_recalculates_stock()
    {
        $product = Product::factory()->create(['stock' => 10]);
        $customer = Customer::factory()->create();
        
        // Initial order: 2 items. Remaining stock: 8
        $order = Order::create([
            'customer_id' => $customer->id,
            'order_date' => now(),
            'status' => 'Pendiente',
            'total_amount' => 100
        ]);
        
        // Use logic similar to controller to set initial state correctly if factory not used fully
        $product->decrement('stock', 2);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 2, 'unit_price' => 50, 'subtotal' => 100]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);

        // Update order: Change quantity to 5.
        // Logic: Restore 2 (Sock=10). Deduct 5 (Stock=5).
        $response = $this->putJson("/api/orders/{$order->id}", [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'status' => 'Pendiente',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_order_deletion_restores_stock()
    {
        $product = Product::factory()->create(['stock' => 10]);
        $customer = Customer::factory()->create();
        
        // Create order with 3 items. Stock becomes 7.
        $order = Order::create([
            'customer_id' => $customer->id,
            'order_date' => now(),
            'status' => 'Pendiente',
            'total_amount' => 150
        ]);
        $product->decrement('stock', 3);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 3, 'unit_price' => 50, 'subtotal' => 150]);

        $this->assertEquals(7, $product->fresh()->stock);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        // Stock should be restored to 10
        $this->assertEquals(10, $product->fresh()->stock);
    }
}
