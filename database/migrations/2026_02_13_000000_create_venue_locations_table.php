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
        Schema::create('venue_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venue_id');
            $table->string('name'); // e.g., "Main Hall", "Computer Room", "Auditorium"
            $table->text('amenities')->nullable(); // e.g., "Court, Wi-Fi, Restrooms"
            $table->text('facilities')->nullable(); // e.g., "Tables, Chairs, Sound system"
            $table->unsignedInteger('capacity')->nullable(); // Capacity of this specific location
            $table->timestamps();

            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_locations');
    }
};
