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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('whatsapp_configs', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('marketing_assets', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
