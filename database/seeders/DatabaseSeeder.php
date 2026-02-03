<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create customers with Spanish data
        $customers = [
            ['name' => 'Juan Pérez', 'email' => 'juan.perez@gmail.com', 'phone' => '5512345678'],
            ['name' => 'María García', 'email' => 'maria.garcia@hotmail.com', 'phone' => '5598765432'],
            ['name' => 'Carlos López', 'email' => 'carlos.lopez@outlook.com', 'phone' => '5545678901'],
            ['name' => 'Ana Martínez', 'email' => 'ana.martinez@empresa.com', 'phone' => '5578901234'],
            ['name' => 'Luis Rodríguez', 'email' => 'luis.rodriguez@tech.com', 'phone' => '5523456789'],
            ['name' => 'Elena Fernández', 'email' => 'elena.fernandez@web.com', 'phone' => '5567890123'],
            ['name' => 'Miguel Ángel Torres', 'email' => 'miguel.torres@studio.com', 'phone' => '5501234567'],
            ['name' => 'Sofía Ramírez', 'email' => 'sofia.ramirez@design.com', 'phone' => '5534567890'],
            ['name' => 'David González', 'email' => 'david.gonzalez@dev.com', 'phone' => '5589012345'],
            ['name' => 'Lucía Sánchez', 'email' => 'lucia.sanchez@marketing.com', 'phone' => '5556789012'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create products with Spanish data
        $products = [
            ['name' => 'Laptop Gamer Pro X15', 'sku' => 'LAP-GAM-001', 'price' => 25500.00, 'stock' => 12],
            ['name' => 'Mouse Inalámbrico Ergonómico', 'sku' => 'MSE-WRL-002', 'price' => 450.50, 'stock' => 50],
            ['name' => 'Teclado Mecánico RGB', 'sku' => 'KEY-RGB-003', 'price' => 1200.00, 'stock' => 30],
            ['name' => 'Monitor UltraWide 34 Pulgadas', 'sku' => 'MON-UWD-004', 'price' => 8900.00, 'stock' => 8],
            ['name' => 'Disco SSD NVMe 1TB', 'sku' => 'SSD-1TB-005', 'price' => 1850.00, 'stock' => 45],
            ['name' => 'Memoria RAM 16GB DDR4', 'sku' => 'RAM-16G-006', 'price' => 1400.00, 'stock' => 60],
            ['name' => 'Auriculares Cancelación de Ruido', 'sku' => 'HDP-ANC-007', 'price' => 3200.00, 'stock' => 25],
            ['name' => 'Silla Ergonómica de Oficina', 'sku' => 'CHR-OFF-008', 'price' => 4500.00, 'stock' => 10],
            ['name' => 'Cámara Web HD 1080p', 'sku' => 'CAM-FHD-009', 'price' => 950.00, 'stock' => 35],
            ['name' => 'Hub USB-C 7 en 1', 'sku' => 'HUB-USB-010', 'price' => 750.00, 'stock' => 40],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
