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
        Schema::create('wholesalers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // HHC, Markaz, Zarya, CJ
            $table->string('website_url')->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesalers');
    }
};
