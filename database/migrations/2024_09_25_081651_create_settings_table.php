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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('multilevel_size')->default(5);
            $table->integer('direct_points')->default(10);
            $table->integer('downline_points')->default(5);
            $table->integer('activity_points')->default(10);
            $table->integer('channel_size')->default(5);
            $table->integer('last_channel')->default(5);
            $table->integer('verification_expiry')->default(1800);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
