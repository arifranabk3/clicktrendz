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
        Schema::table('products', function (Blueprint $table) {
            // Rename existing to match specification if they exist, or add new
            if (Schema::hasColumn('products', 'name') && !Schema::hasColumn('products', 'title')) {
                $table->renameColumn('name', 'title');
            }
            if (Schema::hasColumn('products', 'supplier_price') && !Schema::hasColumn('products', 'sourcing_price')) {
                $table->renameColumn('supplier_price', 'sourcing_price');
            }
            if (Schema::hasColumn('products', 'price') && !Schema::hasColumn('products', 'selling_price')) {
                $table->renameColumn('price', 'selling_price');
            }
            if (Schema::hasColumn('products', 'image') && !Schema::hasColumn('products', 'image_url')) {
                $table->renameColumn('image', 'image_url');
            }
            if (Schema::hasColumn('products', 'stock_quantity') && !Schema::hasColumn('products', 'stock_count')) {
                $table->renameColumn('stock_quantity', 'stock_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->renameColumn('sourcing_price', 'supplier_price');
            $table->renameColumn('selling_price', 'price');
            $table->renameColumn('image_url', 'image');
            $table->renameColumn('stock_count', 'stock_quantity');
        });
    }
};
