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
        Schema::table('venue_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('venue_bookings', 'venue_location_id')) {
                $table->unsignedBigInteger('venue_location_id')->nullable()->after('venue_id');
                $table->foreign('venue_location_id')->references('id')->on('venue_locations')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venue_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('venue_bookings', 'venue_location_id')) {
                $table->dropForeign(['venue_location_id']);
                $table->dropColumn('venue_location_id');
            }
        });
    }
};
