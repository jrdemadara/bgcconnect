<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deleted_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('original_id'); // ID of the deleted user
            $table->string('code')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->bigInteger('referred_by')->nullable();
            $table->integer('points')->nullable();
            $table->integer('level')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->nullable();
            $table->integer('id_status')->nullable();
            $table->timestamp('deleted_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_users');
    }
};
