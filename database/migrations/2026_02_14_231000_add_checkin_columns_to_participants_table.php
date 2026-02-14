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
        Schema::table('participants', function (Blueprint $table) {
            $table->string('invitation_code')->nullable()->unique()->after('status');
            $table->string('checkin_token_hash', 64)->nullable()->index()->after('invitation_code');
            $table->text('checkin_token_encrypted')->nullable()->after('checkin_token_hash');
            $table->timestamp('checked_in_at')->nullable()->after('registered_at');
            $table->foreignId('checked_in_by')->nullable()->after('checked_in_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checked_in_by');
            $table->dropUnique('participants_invitation_code_unique');
            $table->dropIndex('participants_checkin_token_hash_index');
            $table->dropColumn(['invitation_code', 'checkin_token_hash', 'checkin_token_encrypted', 'checked_in_at']);
        });
    }
};
