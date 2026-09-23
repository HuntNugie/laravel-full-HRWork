<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_terminations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id');
            $table->foreign('employee_id', 'et_employee_fk')
                ->references('id')
                ->on('employees')
                ->restrictOnDelete();

            $table->foreignId('employee_contract_id')->nullable();
            $table->foreign('employee_contract_id', 'et_contract_fk')
                ->references('id')
                ->on('employee_contracts')
                ->restrictOnDelete();

            $table->foreignId('initiated_by')->nullable();
            $table->foreign('initiated_by', 'et_initiated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('initiated_at')->nullable();

            $table->date('proposed_effective_date');
            $table->date('approved_effective_date')->nullable();

            $table->string('reason_type');
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
            $table->foreign('reviewed_by', 'et_reviewed_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();

            $table->foreignId('cancelled_by')->nullable();
            $table->foreign('cancelled_by', 'et_cancelled_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->foreignId('completed_by')->nullable();
            $table->foreign('completed_by', 'et_completed_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['status', 'proposed_effective_date']);
            $table->index(['reason_type', 'status']);
        });

        Schema::create('employee_termination_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('termination_id');
            $table->foreign('termination_id', 'eth_termination_fk')
                ->references('id')
                ->on('employee_terminations')
                ->cascadeOnDelete();

            $table->foreignId('actor_id')->nullable();
            $table->foreign('actor_id', 'eth_actor_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['termination_id', 'created_at']);
        });

        Schema::create('employee_termination_clearances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('termination_id');
            $table->foreign('termination_id', 'etc_termination_fk')
                ->references('id')
                ->on('employee_terminations')
                ->cascadeOnDelete();

            $table->string('category');

            $table->enum('status', [
                'pending',
                'completed',
                'not_applicable',
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->foreignId('verified_by')->nullable();
            $table->foreign('verified_by', 'etc_verified_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['termination_id', 'category']);
            $table->index(['category', 'status']);
        });

        Schema::create('employee_termination_handover_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('termination_id');
            $table->foreign('termination_id', 'ethi_termination_fk')
                ->references('id')
                ->on('employee_terminations')
                ->cascadeOnDelete();

            $table->foreignId('task_id')->nullable();
            $table->foreign('task_id', 'ethi_task_fk')
                ->references('id')
                ->on('tasks')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('handover_to_employee_id')->nullable();
            $table->foreign('handover_to_employee_id', 'ethi_handover_employee_fk')
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
            $table->foreign('verified_by', 'ethi_verified_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['termination_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_termination_handover_items');
        Schema::dropIfExists('employee_termination_clearances');
        Schema::dropIfExists('employee_termination_histories');
        Schema::dropIfExists('employee_terminations');
    }
};
