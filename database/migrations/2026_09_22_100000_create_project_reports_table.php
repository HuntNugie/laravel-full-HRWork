<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('project_reports')) {
            Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_project_id')->constrained('division_projects')->restrictOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->restrictOnDelete();
            $table->foreignId('reported_by')->constrained('employees')->restrictOnDelete();
            $table->enum('report_level', ['supervisor', 'manager']);
            $table->enum('status', ['submitted', 'approved', 'rejected'])->default('submitted');
            $table->text('content');
            $table->timestamps();

            $table->index(['division_project_id', 'report_level', 'status']);
            $table->index(['division_project_id', 'team_id', 'created_at']);
            });
        }

        $this->extendDivisionProjectStatus();
    }

    public function down(): void
    {
        $this->restoreDivisionProjectStatus();

        Schema::dropIfExists('project_reports');
    }

    private function extendDivisionProjectStatus(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true) && Schema::hasTable('division_projects')) {
            DB::statement(
                "ALTER TABLE division_projects MODIFY status ENUM(
                    'draft',
                    'in_progress',
                    'ready_for_review',
                    'submitted_to_manager',
                    'revision_required',
                    'manager_approved',
                    'submitted_to_gm',
                    'completed'
                ) NOT NULL DEFAULT 'draft'"
            );
        }
    }

    private function restoreDivisionProjectStatus(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true) && Schema::hasTable('division_projects')) {
            DB::statement("UPDATE division_projects SET status = CASE
                WHEN status IN ('submitted_to_manager', 'manager_approved', 'submitted_to_gm') THEN 'in_progress'
                ELSE status
            END");

            DB::statement(
                "ALTER TABLE division_projects MODIFY status ENUM(
                    'draft',
                    'in_progress',
                    'ready_for_review',
                    'revision_required',
                    'completed'
                ) NOT NULL DEFAULT 'draft'"
            );
        }
    }
};
