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
        Schema::table('agent_logs', function (Blueprint $table) {
            $table->string('activity_type')->nullable()->change();
            $table->text('message')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_logs', function (Blueprint $table) {
            $table->string('activity_type')->nullable(false)->change();
            $table->text('message')->nullable(false)->change();
        });
    }
};
