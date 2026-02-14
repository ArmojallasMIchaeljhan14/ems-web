<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_comments', function (Blueprint $table) {
            // Drop the old post_id foreign key constraint if it exists
            $table->dropForeign(['post_id']);
            // Drop the old post_id column
            $table->dropColumn('post_id');
        });
    }

    public function down(): void
    {
        Schema::table('post_comments', function (Blueprint $table) {
            // Add back the post_id column for rollback
            $table->foreignId('post_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }
};
