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
            $table->string('lastname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('extension')->nullable();
            // media filenames
            $table->string('precinct_number')->nullable();
            $table->string('avatar')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_card_front')->nullable();
            $table->string('id_card_back')->nullable();
            // address
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('municipality_city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            // personal
            $table->string('gender')->nullable();
            $table->string('birthdate')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('religion')->nullable();
            $table->string('tribe')->nullable();
            $table->string('industry_sector')->nullable();
            $table->string('occupation')->nullable();
            $table->string('income_level')->nullable();

            $table->string('affiliation')->nullable();
            $table->string('facebook')->nullable();

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
