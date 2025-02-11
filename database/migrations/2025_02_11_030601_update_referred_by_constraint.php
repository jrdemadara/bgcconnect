<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']); // Remove old constraint
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']); // Rollback
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
