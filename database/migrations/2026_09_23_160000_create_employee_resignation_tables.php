<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_resignations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreign('employee_id', 'er_employee_fk')
                ->references('id')
                ->on('employees')
                ->restrictOnDelete();

            $table->foreignId('employee_contract_id')->nullable();
            $table->foreign('employee_contract_id', 'er_contract_fk')
                ->references('id')
                ->on('employee_contracts')
                ->restrictOnDelete();

            $table->foreignId('submitted_by')->nullable();
            $table->foreign('submitted_by', 'er_submitted_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->date('proposed_last_working_date');
            $table->date('approved_last_working_date')->nullable();

            $table->text('reason');
            $table->text('notes')->nullable();

            $table->enum('status', [
                'submitted',
                'approved',
                'rejected',
                'cancelled',
                'completed',
            ])->default('submitted');

            $table->foreignId('reviewed_by')->nullable();
            $table->foreign('reviewed_by', 'er_reviewed_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->foreignId('cancelled_by')->nullable();
            $table->foreign('cancelled_by', 'er_cancelled_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();

            $table->foreignId('completed_by')->nullable();
            $table->foreign('completed_by', 'er_completed_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('completed_at')->nullable();

            $table->text('exit_interview_notes')->nullable();
            $table->foreignId('exit_interview_by')->nullable();
            $table->foreign('exit_interview_by', 'er_exit_interview_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->timestamp('exit_interview_at')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['status', 'proposed_last_working_date']);
        });

        Schema::create('employee_resignation_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resignation_id');
            $table->foreign('resignation_id', 'erh_resignation_fk')
                ->references('id')
                ->on('employee_resignations')
                ->cascadeOnDelete();

            $table->foreignId('actor_id')->nullable();
            $table->foreign('actor_id', 'erh_actor_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['resignation_id', 'created_at']);
        });

        Schema::create('employee_resignation_clearances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resignation_id');
            $table->foreign('resignation_id', 'erc_resignation_fk')
                ->references('id')
                ->on('employee_resignations')
                ->cascadeOnDelete();

            $table->string('category');
            $table->enum('status', [
                'pending',
                'completed',
                'not_applicable',
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->foreignId('verified_by')->nullable();
            $table->foreign('verified_by', 'erc_verified_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['resignation_id', 'category']);
            $table->index(['category', 'status']);
        });

        Schema::create('employee_resignation_handover_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resignation_id');
            $table->foreign('resignation_id', 'erhi_resignation_fk')
                ->references('id')
                ->on('employee_resignations')
                ->cascadeOnDelete();

            $table->foreignId('task_id')->nullable();
            $table->foreign('task_id', 'erhi_task_fk')
                ->references('id')
                ->on('tasks')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('handover_to_employee_id')->nullable();
            $table->foreign('handover_to_employee_id', 'erhi_handover_employee_fk')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'not_applicable',
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->foreignId('verified_by')->nullable();
            $table->foreign('verified_by', 'erhi_verified_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['resignation_id', 'status']);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('resignation_id')
                ->nullable()
                ->after('employee_contract_id');

            $table->foreign('resignation_id', 'payroll_resignation_fk')
                ->references('id')
                ->on('employee_resignations')
                ->nullOnDelete();

            $table->index('resignation_id');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['resignation_id']);
            $table->dropIndex(['resignation_id']);
            $table->dropColumn('resignation_id');
        });

        Schema::dropIfExists('employee_resignation_handover_items');
        Schema::dropIfExists('employee_resignation_clearances');
        Schema::dropIfExists('employee_resignation_histories');
        Schema::dropIfExists('employee_resignations');
    }
};
