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
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            $table->foreignId('employee_contract_id')
                ->nullable()
                ->constrained('employee_contracts')
                ->restrictOnDelete();

            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
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

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();

            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['status', 'proposed_last_working_date']);
        });

        Schema::create('employee_resignation_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resignation_id')
                ->constrained('employee_resignations')
                ->cascadeOnDelete();

            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['resignation_id', 'created_at']);
        });

        Schema::create('employee_resignation_clearances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resignation_id')
                ->constrained('employee_resignations')
                ->cascadeOnDelete();

            $table->string('category');
            $table->enum('status', [
                'pending',
                'completed',
                'not_applicable',
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['resignation_id', 'category']);
            $table->index(['category', 'status']);
        });

        Schema::create('employee_resignation_handover_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resignation_id')
                ->constrained('employee_resignations')
                ->cascadeOnDelete();

            $table->foreignId('task_id')
                ->nullable()
                ->constrained('tasks')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('handover_to_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'not_applicable',
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['resignation_id', 'status']);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('resignation_id')
                ->nullable()
                ->after('employee_contract_id')
                ->constrained('employee_resignations')
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
