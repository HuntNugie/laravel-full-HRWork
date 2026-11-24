<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->decimal('min_salary_daily', 15, 2)->default(0);
            $table->enum('is_active', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        Schema::create('position_jobdesks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->string('jobdesk');
            $table->timestamps();
        });
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_code')->unique();
            $table->string('name');
            $table->string('short_name');
            $table->timestamps();
        });
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('position_id')->nullable()->constrained('positions')->restrictOnDelete();
            $table->enum('status_employee', ['onboarding', 'active', 'inactive', 'resign', 'terminated'])->default('onboarding')->index();
            $table->timestamps();
        });
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained('employees')->cascadeOnDelete();
            $table->enum('gender', ['male', 'female']);
            $table->string('phone_number')->nullable();
            $table->string('nik', 16)->unique()->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_address')->nullable();
            $table->timestamps();
        });
        Schema::create('employee_profile_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->unique()->constrained('employee_profiles')->cascadeOnDelete();
            $table->string('full_address');
            $table->string('village_code', 13)->nullable();
            $table->foreign('village_code')->references('code')->on('villages')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('employee_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->unique()->constrained('employee_profiles')->cascadeOnDelete();
            $table->foreignId('bank_id')->constrained('banks')->restrictOnDelete();
            $table->string('account_number');
            $table->string('account_holder');
            $table->timestamps();
        });
        Schema::create('employee_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('old_status', ['onboarding', 'active', 'inactive', 'resign', 'terminated'])->nullable();
            $table->enum('new_status', ['onboarding', 'active', 'inactive', 'resign', 'terminated']);
            $table->date('effective_date');
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('default_days')->default(0);
            $table->enum('gender', ['all', 'male', 'female'])->default('all');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        Schema::create('contract_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('year');
            $table->unsignedInteger('month');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique(['year', 'month']);
        });
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('position_id')->constrained('positions')->restrictOnDelete();
            $table->string('contract_number')->unique();
            $table->string('position_name');
            $table->enum('employement_type', ['pkwt', 'pkwtt', 'intership', 'freelance']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary_daily', 12, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->text('notes')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('end_reason')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'status']);
            $table->index(['status', 'end_date']);
        });
        Schema::create('contract_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_contract_id')->constrained('employee_contracts')->cascadeOnDelete();
            $table->foreignId('benefit_id')->constrained('benefits')->restrictOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('name_snapshot');
            $table->text('description_snapshot')->nullable();
            $table->timestamps();
            $table->unique(['employee_contract_id', 'benefit_id']);
        });
        Schema::create('contract_leave_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_contract_id')->constrained('employee_contracts')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->restrictOnDelete();
            $table->unsignedInteger('days')->default(0);
            $table->string('name_snapshot');
            $table->timestamps();
            $table->unique(['employee_contract_id', 'leave_type_id'], 'contract_leave_entitlement_unique');
        });
    }

    public function down(): void
    {
        foreach (['contract_leave_entitlements', 'contract_benefits', 'employee_contracts', 'contract_sequences', 'leave_types', 'benefits', 'employee_status_histories', 'employee_bank_accounts', 'employee_profile_addresses', 'employee_profiles', 'employees', 'banks', 'position_jobdesks', 'positions'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
