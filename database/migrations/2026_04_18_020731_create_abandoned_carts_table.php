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
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('customer_phone')->nullable();
            $table->json('cart_data');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->boolean('is_notified')->default(false);
            $table->timestamp('last_activity_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
