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
        Schema::table('support_tickets', function (Blueprint $table): void {
            $table->foreignId('event_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('priority', 16)->default('medium')->after('status');

            $table->index(['status', 'priority']);
            $table->index('event_id');
        });

        Schema::table('support_messages', function (Blueprint $table): void {
            $table->boolean('is_system')->default(false)->after('body');
            $table->index('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table): void {
            $table->dropIndex(['is_system']);
            $table->dropColumn('is_system');
        });

        Schema::table('support_tickets', function (Blueprint $table): void {
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['event_id']);
            $table->dropConstrainedForeignId('event_id');
            $table->dropColumn('priority');
        });
    }
};
