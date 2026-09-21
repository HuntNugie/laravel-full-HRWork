<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createOrUpgradeMasterProjects();
        $this->createOrUpgradeDivisionProjects();
        $this->createIfMissingProjectTeams();
        $this->createOrUpgradeTasks();
        $this->createIfMissingTaskReviews();
        $this->createIfMissingProgressUpdates();
        $this->createOrUpgradeProjectReviews();
        $this->createIfMissingAudits();
    }

    public function down(): void
    {
        $isUpgradeFromV2 = Schema::hasTable('migrations')
            && DB::table('migrations')
                ->where('migration', '2026_09_21_000001_create_work_management_tables')
                ->exists();

        if (! $isUpgradeFromV2) {
            Schema::dropIfExists('work_management_audits');
            Schema::dropIfExists('project_reviews');
            Schema::dropIfExists('project_progress_updates');
            Schema::dropIfExists('task_reviews');
            Schema::dropIfExists('tasks');
            Schema::dropIfExists('project_teams');
            Schema::dropIfExists('division_projects');
            Schema::dropIfExists('master_projects');

            return;
        }

        Schema::dropIfExists('work_management_audits');

        if (Schema::hasTable('project_reviews') && Schema::hasColumn('project_reviews', 'master_project_id')) {
            Schema::table('project_reviews', function (Blueprint $table) {
                $table->dropForeign(['master_project_id']);
                $table->dropColumn('master_project_id');
            });
        }

        if (Schema::hasTable('tasks')) {
            $this->downgradeTaskStatusToV2();

            foreach (['progress', 'result', 'blocked_reason'] as $column) {
                if (Schema::hasColumn('tasks', $column)) {
                    Schema::table('tasks', fn (Blueprint $table) => $table->dropColumn($column));
                }
            }
        }

        if (Schema::hasTable('division_projects')) {
            foreach (['manager_id', 'is_required', 'manual_progress'] as $column) {
                if (Schema::hasColumn('division_projects', $column)) {
                    Schema::table('division_projects', function (Blueprint $table) use ($column) {
                        if ($column === 'manager_id') {
                            $table->dropForeign(['manager_id']);
                        }
                        $table->dropColumn($column);
                    });
                }
            }
        }

        if (Schema::hasTable('master_projects')) {
            foreach (['approved_by', 'approved_at'] as $column) {
                if (Schema::hasColumn('master_projects', $column)) {
                    Schema::table('master_projects', function (Blueprint $table) use ($column) {
                        if ($column === 'approved_by') {
                            $table->dropForeign(['approved_by']);
                        }
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }

    private function createOrUpgradeMasterProjects(): void
    {
        if (! Schema::hasTable('master_projects')) {
            Schema::create('master_projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('created_by')->constrained('employees')->restrictOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->enum('status', ['draft', 'in_progress', 'ready_for_review', 'completed'])->default('draft');
                $table->foreignId('approved_by')->nullable()->constrained('employees')->restrictOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                $table->index(['status', 'due_date']);
            });

            return;
        }

        Schema::table('master_projects', function (Blueprint $table) {
            if (! Schema::hasColumn('master_projects', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('employees')->restrictOnDelete();
            }

            if (! Schema::hasColumn('master_projects', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
        });
    }

    private function createOrUpgradeDivisionProjects(): void
    {
        if (! Schema::hasTable('division_projects')) {
            Schema::create('division_projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('master_project_id')->constrained('master_projects')->restrictOnDelete();
                $table->foreignId('divisi_id')->constrained('divisis')->restrictOnDelete();
                $table->foreignId('manager_id')->constrained('employees')->restrictOnDelete();
                $table->foreignId('created_by')->constrained('employees')->restrictOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->boolean('is_required')->default(true);
                $table->unsignedTinyInteger('manual_progress')->default(0);
                $table->enum('status', ['draft', 'in_progress', 'ready_for_review', 'revision_required', 'completed'])->default('draft');
                $table->timestamps();
                $table->unique(['master_project_id', 'divisi_id']);
                $table->index(['divisi_id', 'status']);
                $table->index(['manager_id', 'status']);
            });

            return;
        }

        Schema::table('division_projects', function (Blueprint $table) {
            if (! Schema::hasColumn('division_projects', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->constrained('employees')->restrictOnDelete();
            }

            if (! Schema::hasColumn('division_projects', 'is_required')) {
                $table->boolean('is_required')->default(true);
            }

            if (! Schema::hasColumn('division_projects', 'manual_progress')) {
                $table->unsignedTinyInteger('manual_progress')->default(0);
            }

            if (! Schema::hasColumn('division_projects', 'manager_id_status_index')) {
                $table->index(['manager_id', 'status']);
            }
        });

        if (Schema::hasColumn('division_projects', 'reported_progress')
            && Schema::hasColumn('division_projects', 'manual_progress')) {
            DB::statement('UPDATE division_projects SET manual_progress = COALESCE(reported_progress, 0)');
        }

        DB::statement('
            UPDATE division_projects dp
            INNER JOIN divisis d ON d.id = dp.divisi_id
            SET dp.manager_id = d.manager_id
            WHERE dp.manager_id IS NULL
        ');
    }

    private function createIfMissingProjectTeams(): void
    {
        if (Schema::hasTable('project_teams')) {
            return;
        }

        Schema::create('project_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
            $table->foreignId('team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('assigned_by')->constrained('employees')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['division_project_id', 'team_id']);
        });
    }

    private function createOrUpgradeTasks(): void
    {
        if (! Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
                $table->foreignId('team_id')->constrained('teams')->restrictOnDelete();
                $table->foreignId('assignee_id')->constrained('employees')->restrictOnDelete();
                $table->foreignId('created_by')->constrained('employees')->restrictOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('progress')->default(0);
                $table->text('result')->nullable();
                $table->text('blocked_reason')->nullable();
                $table->date('due_date')->nullable();
                $table->enum('status', ['to_do', 'in_progress', 'in_review', 'blocked', 'done', 'cancelled'])->default('to_do');
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->index(['assignee_id', 'status']);
                $table->index(['team_id', 'status']);
                $table->index(['division_project_id', 'status']);
            });

            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'progress')) {
                $table->unsignedTinyInteger('progress')->default(0)->after('description');
            }

            if (! Schema::hasColumn('tasks', 'result')) {
                $table->text('result')->nullable()->after('progress');
            }

            if (! Schema::hasColumn('tasks', 'blocked_reason')) {
                $table->text('blocked_reason')->nullable()->after('result');
            }

            if (! Schema::hasColumn('tasks', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('submitted_at');
            }
        });

        if (Schema::hasColumn('tasks', 'status')) {
            DB::statement("
                UPDATE tasks
                SET status = CASE status
                    WHEN 'todo' THEN 'to_do'
                    WHEN 'submitted' THEN 'in_review'
                    WHEN 'approved' THEN 'done'
                    ELSE status
                END
            ");

            $this->upgradeTaskStatusEnum();
        }
    }

    private function createIfMissingTaskReviews(): void
    {
        if (Schema::hasTable('task_reviews')) {
            return;
        }

        Schema::create('task_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->restrictOnDelete();
            $table->foreignId('reviewer_id')->constrained('employees')->restrictOnDelete();
            $table->enum('decision', ['approved', 'rejected']);
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->index(['task_id', 'created_at']);
        });
    }

    private function createIfMissingProgressUpdates(): void
    {
        if (Schema::hasTable('project_progress_updates')) {
            return;
        }

        Schema::create('project_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
            $table->foreignId('reported_by')->constrained('employees')->restrictOnDelete();
            $table->unsignedTinyInteger('progress');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['division_project_id', 'created_at']);
        });
    }

    private function createOrUpgradeProjectReviews(): void
    {
        if (! Schema::hasTable('project_reviews')) {
            Schema::create('project_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_project_id')->nullable()->constrained('division_projects')->restrictOnDelete();
                $table->foreignId('master_project_id')->nullable()->constrained('master_projects')->restrictOnDelete();
                $table->foreignId('reviewer_id')->constrained('employees')->restrictOnDelete();
                $table->enum('reviewer_level', ['manager', 'general_manager']);
                $table->enum('decision', ['approved', 'rejected']);
                $table->text('feedback')->nullable();
                $table->timestamps();
                $table->index(['division_project_id', 'reviewer_level', 'created_at'], 'proj_reviews_div_proj_lvl_created_idx');
                $table->index(['master_project_id', 'reviewer_level', 'created_at'], 'proj_reviews_master_lvl_created_idx');
            });

            return;
        }

        Schema::table('project_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('project_reviews', 'master_project_id')) {
                $table->foreignId('master_project_id')->nullable()->constrained('master_projects')->restrictOnDelete();
            }
        });
    }

    private function createIfMissingAudits(): void
    {
        if (Schema::hasTable('work_management_audits')) {
            return;
        }

        Schema::create('work_management_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained('employees')->restrictOnDelete();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['actor_id', 'created_at']);
        });
    }

    private function upgradeTaskStatusEnum(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("
                ALTER TABLE tasks
                MODIFY status ENUM('to_do','in_progress','in_review','blocked','done','cancelled')
                NOT NULL DEFAULT 'to_do'
            ");
        }
    }

    private function downgradeTaskStatusToV2(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("
                UPDATE tasks
                SET status = CASE status
                    WHEN 'to_do' THEN 'todo'
                    WHEN 'in_review' THEN 'submitted'
                    WHEN 'done' THEN 'approved'
                    WHEN 'blocked' THEN 'in_progress'
                    WHEN 'cancelled' THEN 'todo'
                    ELSE status
                END
            ");

            DB::statement("
                ALTER TABLE tasks
                MODIFY status ENUM('todo','in_progress','submitted','approved')
                NOT NULL DEFAULT 'todo'
            ");
        }
    }
};
