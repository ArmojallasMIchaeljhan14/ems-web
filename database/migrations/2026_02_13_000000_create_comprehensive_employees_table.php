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
        // Check if the table exists and has the basic columns
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                
                // Personal Information
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name');
                $table->date('date_of_birth')->nullable();
                $table->enum('gender', ['Male', 'Female', 'Other', 'Prefer not to say'])->nullable();
                $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed', 'Prefer not to say'])->nullable();
                $table->string('nationality')->nullable();

                // Contact Information
                $table->string('email')->unique();
                $table->string('phone_number')->nullable();
                $table->string('mobile_number')->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_phone')->nullable();
                $table->string('emergency_contact_relationship')->nullable();

                // Address Information
                $table->string('street_address')->nullable();
                $table->string('city')->nullable();
                $table->string('state_province')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
                $table->string('residential_type')->nullable(); // Owned, Rented, etc.

                // Employment Information
                $table->string('employee_id_number')->unique();
                $table->string('department')->nullable();
                $table->string('position_title')->nullable();
                $table->text('job_description')->nullable();
                $table->enum('employment_status', ['Active', 'Inactive', 'On Leave', 'Terminated', 'Retired'])->default('Active');
                $table->date('hire_date')->nullable();
                $table->date('probation_end_date')->nullable();
                $table->enum('contract_type', ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Intern'])->nullable();

                // Compensation & Banking
                $table->decimal('hourly_rate', 10, 2)->nullable();
                $table->decimal('salary', 12, 2)->nullable();
                $table->enum('salary_frequency', ['Monthly', 'Bi-weekly', 'Weekly', 'Annual'])->default('Monthly');
                $table->string('bank_account_holder_name')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_routing_number')->nullable();
                $table->string('bank_branch')->nullable();

                // Tax & Legal
                $table->string('tax_id_number')->nullable(); // Encrypted
                $table->string('id_document_type')->nullable(); // Passport, National ID, etc.
                $table->string('id_document_number')->nullable(); // Encrypted

                // Additional Information
                $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');
                $table->text('notes')->nullable();
                $table->string('profile_photo_path')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Indexes for performance
                $table->index('department');
                $table->index('employment_status');
                $table->index('hire_date');
                $table->index('manager_id');
            });
        } else {
            // Add columns if they don't exist
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'middle_name')) {
                    $table->string('middle_name')->nullable()->after('first_name');
                }
                if (!Schema::hasColumn('employees', 'date_of_birth')) {
                    $table->date('date_of_birth')->nullable();
                }
                if (!Schema::hasColumn('employees', 'gender')) {
                    $table->enum('gender', ['Male', 'Female', 'Other', 'Prefer not to say'])->nullable();
                }
                if (!Schema::hasColumn('employees', 'marital_status')) {
                    $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed', 'Prefer not to say'])->nullable();
                }
                if (!Schema::hasColumn('employees', 'nationality')) {
                    $table->string('nationality')->nullable();
                }
                if (!Schema::hasColumn('employees', 'phone_number')) {
                    $table->string('phone_number')->nullable();
                }
                if (!Schema::hasColumn('employees', 'mobile_number')) {
                    $table->string('mobile_number')->nullable();
                }
                if (!Schema::hasColumn('employees', 'emergency_contact_name')) {
                    $table->string('emergency_contact_name')->nullable();
                }
                if (!Schema::hasColumn('employees', 'emergency_contact_phone')) {
                    $table->string('emergency_contact_phone')->nullable();
                }
                if (!Schema::hasColumn('employees', 'emergency_contact_relationship')) {
                    $table->string('emergency_contact_relationship')->nullable();
                }
                if (!Schema::hasColumn('employees', 'street_address')) {
                    $table->string('street_address')->nullable();
                }
                if (!Schema::hasColumn('employees', 'city')) {
                    $table->string('city')->nullable();
                }
                if (!Schema::hasColumn('employees', 'state_province')) {
                    $table->string('state_province')->nullable();
                }
                if (!Schema::hasColumn('employees', 'postal_code')) {
                    $table->string('postal_code')->nullable();
                }
                if (!Schema::hasColumn('employees', 'country')) {
                    $table->string('country')->nullable();
                }
                if (!Schema::hasColumn('employees', 'residential_type')) {
                    $table->string('residential_type')->nullable();
                }
                if (!Schema::hasColumn('employees', 'position_title')) {
                    $table->string('position_title')->nullable();
                }
                if (!Schema::hasColumn('employees', 'job_description')) {
                    $table->text('job_description')->nullable();
                }
                if (!Schema::hasColumn('employees', 'employment_status')) {
                    $table->enum('employment_status', ['Active', 'Inactive', 'On Leave', 'Terminated', 'Retired'])->default('Active');
                }
                if (!Schema::hasColumn('employees', 'probation_end_date')) {
                    $table->date('probation_end_date')->nullable();
                }
                if (!Schema::hasColumn('employees', 'contract_type')) {
                    $table->enum('contract_type', ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Intern'])->nullable();
                }
                if (!Schema::hasColumn('employees', 'hourly_rate')) {
                    $table->decimal('hourly_rate', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('employees', 'salary')) {
                    $table->decimal('salary', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('employees', 'salary_frequency')) {
                    $table->enum('salary_frequency', ['Monthly', 'Bi-weekly', 'Weekly', 'Annual'])->default('Monthly');
                }
                if (!Schema::hasColumn('employees', 'bank_account_holder_name')) {
                    $table->string('bank_account_holder_name')->nullable();
                }
                if (!Schema::hasColumn('employees', 'bank_account_number')) {
                    $table->string('bank_account_number')->nullable();
                }
                if (!Schema::hasColumn('employees', 'bank_name')) {
                    $table->string('bank_name')->nullable();
                }
                if (!Schema::hasColumn('employees', 'bank_routing_number')) {
                    $table->string('bank_routing_number')->nullable();
                }
                if (!Schema::hasColumn('employees', 'bank_branch')) {
                    $table->string('bank_branch')->nullable();
                }
                if (!Schema::hasColumn('employees', 'tax_id_number')) {
                    $table->string('tax_id_number')->nullable();
                }
                if (!Schema::hasColumn('employees', 'id_document_type')) {
                    $table->string('id_document_type')->nullable();
                }
                if (!Schema::hasColumn('employees', 'id_document_number')) {
                    $table->string('id_document_number')->nullable();
                }
                if (!Schema::hasColumn('employees', 'manager_id')) {
                    $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');
                }
                if (!Schema::hasColumn('employees', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('employees', 'profile_photo_path')) {
                    $table->string('profile_photo_path')->nullable();
                }
                if (!Schema::hasColumn('employees', 'start_date')) {
                    $table->date('start_date')->nullable();
                }
                if (!Schema::hasColumn('employees', 'end_date')) {
                    $table->date('end_date')->nullable();
                }
                if (!Schema::hasColumn('employees', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
