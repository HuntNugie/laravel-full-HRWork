<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {

            $table->id();


            // =====================================================
            // RELATIONSHIPS
            // =====================================================

            $table->foreignId('payroll_period_id')
                ->constrained('payroll_periods')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            $table->foreignId('employee_contract_id')
                ->constrained('employee_contracts')
                ->restrictOnDelete();


            // =====================================================
            // SNAPSHOT CONTRACT
            // =====================================================

            $table->string('position_name')->nullable();

            $table->decimal('salary_daily', 15, 2)->default(0);


            // =====================================================
            // ATTENDANCE SNAPSHOT
            // =====================================================

            $table->unsignedInteger('working_days')->default(0);

            $table->unsignedInteger('present_days')->default(0);

            $table->unsignedInteger('late_days')->default(0);

            $table->unsignedInteger('absent_days')->default(0);

            $table->unsignedInteger('paid_leave_days')->default(0);

            $table->unsignedInteger('unpaid_leave_days')->default(0);

            $table->unsignedInteger('paid_days')->default(0);


            // =====================================================
            // PAYROLL TOTAL
            // =====================================================

            $table->decimal('gross_amount', 15, 2)->default(0);

            $table->decimal('deduction_amount', 15, 2)->default(0);

            $table->decimal('net_amount', 15, 2)->default(0);


            // =====================================================
            // STATUS
            // =====================================================

            $table->enum('status', [
                'draft',
                'processed',
                'paid',
                'cancelled',
            ])->default('draft');


            // =====================================================
            // NOTES
            // =====================================================

            $table->text('notes')->nullable();


            // =====================================================
            // PROCESS / PAYMENT
            // =====================================================

            $table->timestamp('processed_at')->nullable();

            $table->timestamp('paid_at')->nullable();


            $table->timestamps();


            // =====================================================
            // CONSTRAINT
            // =====================================================

            $table->unique(
                ['payroll_period_id', 'employee_id'],
                'payroll_period_employee_unique'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
