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
        Schema::create('marketing_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->text('ad_copy_hook')->nullable();
            $table->text('ad_copy_body')->nullable();
            $table->text('ad_copy_cta')->nullable();
            $table->string('headline')->nullable();
            $table->string('target_audience')->nullable();
            $table->string('platform')->nullable();
            $table->json('image_prompts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_assets');
    }
};
