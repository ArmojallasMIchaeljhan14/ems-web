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
        Schema::table('event_posts', function (Blueprint $table) {
            $table->boolean('ai_generated_content')->default(false)->after('ai_prompt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_posts', function (Blueprint $table) {
            $table->dropColumn('ai_generated_content');
        });
    }
};
