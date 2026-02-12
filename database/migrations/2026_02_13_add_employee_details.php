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
        Schema::table('employees', function (Blueprint $table) {
            // Personal Information
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

            // Contact Information
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

            // Address Information
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

            // Employment Information
            if (!Schema::hasColumn('employees', 'position_title')) {
                $table->string('position_title')->nullable();
            }
            if (!Schema::hasColumn('employees', 'job_description')) {
                $table->text('job_description')->nullable();
            }
            if (!Schema::hasColumn('employees', 'employment_status')) {
                $table->enum('employment_status', ['Active', 'Inactive', 'On Leave', 'Terminated', 'Retired'])->default('Active');
            }
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable();
            }
            if (!Schema::hasColumn('employees', 'probation_end_date')) {
                $table->date('probation_end_date')->nullable();
            }
            if (!Schema::hasColumn('employees', 'contract_type')) {
                $table->enum('contract_type', ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Intern'])->nullable();
            }

            // Legal Information
            if (!Schema::hasColumn('employees', 'tax_id_number')) {
                $table->string('tax_id_number')->nullable();
            }
            if (!Schema::hasColumn('employees', 'id_document_type')) {
                $table->string('id_document_type')->nullable();
            }
            if (!Schema::hasColumn('employees', 'id_document_number')) {
                $table->string('id_document_number')->nullable();
            }

            // Additional Information
            if (!Schema::hasColumn('employees', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable();
                $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = [
                'middle_name', 'date_of_birth', 'gender', 'marital_status', 'nationality',
                'phone_number', 'mobile_number', 'emergency_contact_name', 'emergency_contact_phone',
                'emergency_contact_relationship', 'street_address', 'city', 'state_province',
                'postal_code', 'country', 'residential_type', 'position_title', 'job_description',
                'employment_status', 'hire_date', 'probation_end_date', 'contract_type',
                'tax_id_number', 'id_document_type', 'id_document_number', 'manager_id',
                'notes', 'profile_photo_path', 'start_date', 'end_date', 'deleted_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
