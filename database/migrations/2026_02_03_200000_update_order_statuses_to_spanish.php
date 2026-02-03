<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 50)->change();
        });

        DB::table('orders')->where('status', 'pending')->update(['status' => 'Pendiente']);
        DB::table('orders')->where('status', 'paid')->update(['status' => 'Pagado']);
        DB::table('orders')->where('status', 'cancelled')->update(['status' => 'Cancelado']);

    }

    public function down(): void
    {
        // Reverse process
        DB::table('orders')->where('status', 'Pendiente')->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'Pagado')->update(['status' => 'paid']);
        DB::table('orders')->where('status', 'Cancelado')->update(['status' => 'cancelled']);
        
        Schema::table('orders', function (Blueprint $table) {
             $table->string('status')->change();
        });
    }
};
