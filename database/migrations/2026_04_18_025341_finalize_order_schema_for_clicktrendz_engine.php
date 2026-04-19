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
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->after('customer_email')->nullable();
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->after('customer_name')->nullable();
            }
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->after('customer_phone')->nullable();
            }
            if (!Schema::hasColumn('orders', 'shipping_city')) {
                $table->string('shipping_city')->after('shipping_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'is_verified')) {
                $table->boolean('is_verified')->default(true)->after('shipping_city');
            }
            if (!Schema::hasColumn('orders', 'selling_price')) {
                $table->decimal('selling_price', 12, 2)->default(0)->after('is_verified');
            }
            if (!Schema::hasColumn('orders', 'sourcing_price')) {
                $table->decimal('sourcing_price', 12, 2)->default(0)->after('selling_price');
            }
            if (!Schema::hasColumn('orders', 'margin_amount')) {
                $table->decimal('margin_amount', 12, 2)->default(0)->after('sourcing_price');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('cod')->after('margin_amount');
            }
            if (!Schema::hasColumn('orders', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained('businesses')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_phone',
                'shipping_address',
                'shipping_city',
                'is_verified',
                'selling_price',
                'sourcing_price',
                'margin_amount',
                'payment_method'
            ]);
        });
    }
};
