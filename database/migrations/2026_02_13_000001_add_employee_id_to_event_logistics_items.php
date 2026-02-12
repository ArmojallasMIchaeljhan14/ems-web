<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_logistics_items', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete()->after('resource_id');
        });
    }

    public function down(): void
    {
        Schema::table('event_logistics_items', function (Blueprint $table) {
            $table->dropForeignIdFor('employees');
            $table->dropColumn('employee_id');
        });
    }
};
