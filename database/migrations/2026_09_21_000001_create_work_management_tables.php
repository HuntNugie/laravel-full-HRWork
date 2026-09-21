<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('employees')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'ready_for_review', 'completed'])->default('draft');
            $table->timestamps();
            $table->index(['status', 'due_date']);
        });

        Schema::create('division_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_project_id')->constrained('master_projects')->restrictOnDelete();
            $table->foreignId('divisi_id')->constrained('divisis')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('employees')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedTinyInteger('reported_progress')->default(0);
            $table->enum('status', ['draft', 'in_progress', 'ready_for_review', 'revision_required', 'completed'])->default('draft');
            $table->timestamps();
            $table->unique(['master_project_id', 'divisi_id']);
            $table->index(['divisi_id', 'status']);
        });

        Schema::create('project_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
            $table->foreignId('team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('assigned_by')->constrained('employees')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['division_project_id', 'team_id']);
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
            $table->foreignId('team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('assignee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('employees')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'submitted', 'approved'])->default('todo');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['assignee_id', 'status']);
            $table->index(['team_id', 'status']);
        });

        Schema::create('task_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->restrictOnDelete();
            $table->foreignId('reviewer_id')->constrained('employees')->restrictOnDelete();
            $table->enum('decision', ['approved', 'rejected']);
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->index(['task_id', 'created_at']);
        });

        Schema::create('project_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
            $table->foreignId('reported_by')->constrained('employees')->restrictOnDelete();
            $table->unsignedTinyInteger('progress');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['division_project_id', 'created_at']);
        });

        Schema::create('project_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
            $table->foreignId('reviewer_id')->constrained('employees')->restrictOnDelete();
            $table->enum('reviewer_level', ['manager', 'general_manager']);
            $table->enum('decision', ['approved', 'rejected']);
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->index(['division_project_id', 'reviewer_level', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_reviews');
        Schema::dropIfExists('project_progress_updates');
        Schema::dropIfExists('task_reviews');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('project_teams');
        Schema::dropIfExists('division_projects');
        Schema::dropIfExists('master_projects');
    }
};
