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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('general'); // general, attendance, event, policy, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            
            // Relations
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Attendance specific data
            $table->json('attendance_data')->nullable();
            $table->timestamp('generated_at')->nullable();
            
            // Metadata
            $table->string('category')->nullable();
            $table->string('tags')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'event_id']);
            $table->index('generated_at');
            $table->index('published_at');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
