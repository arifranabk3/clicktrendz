<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['country_id', 'created_at', 'status'], 'orders_performance_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['country_id', 'is_active', 'wholesaler_id'], 'products_performance_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_performance_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_performance_index');
        });
    }
};
