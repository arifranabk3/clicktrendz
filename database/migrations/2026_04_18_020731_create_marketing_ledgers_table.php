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
        Schema::create('marketing_ledgers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('total_revenue', 15, 2);
            $table->decimal('net_profit', 15, 2);
            $table->decimal('budget_allocated', 15, 2);
            $table->integer('tier')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_ledgers');
    }
};
