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
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('sourcing_price', 12, 2)->nullable();
            $table->decimal('margin_amount', 12, 2)->nullable();
            $table->string('payment_method')->default('cod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id', 'is_active']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id', 'selling_price', 'sourcing_price', 'margin_amount', 'payment_method']);
        });
    }
};
