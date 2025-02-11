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
        Schema::connection('mongodb')->create('logs', function (Blueprint $table) {
            $table->id(); // MongoDB uses _id automatically
            $table->string('type')->nullable();
            $table->json('content'); // Dynamic field (JSON)
            $table->string('ip_address')->index();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('logs');
    }
};
