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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('extension');
            // media filenames
            $table->string('precinct_number');
            $table->string('avatar');
            $table->string('id_type');
            $table->string('id_card_front');
            $table->string('id_card_back');
            // address
            $table->string('region');
            $table->string('province');
            $table->string('municipality_city');
            $table->string('barangay');
            $table->string('street');
            // personal
            $table->string('gender');
            $table->string('birthdate');
            $table->string('civil_status');
            $table->string('blood_type');
            $table->string('religion');
            $table->string('tribe');
            $table->string('industry_sector');
            $table->string('occupation');
            $table->string('income_level');

            $table->string('affiliation');
            $table->string('facebook');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->index('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
