<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Archive, rather than destroy, pre-existing project data.
     * The archive tables remain available for historical investigation.
     */
    private const TABLES = [
        'work_management_audits',
        'project_reports',
        'project_reviews',
        'project_progress_updates',
        'task_reviews',
        'tasks',
        'project_teams',
        'division_projects',
        'master_projects',
    ];

    private const HANDOVER_TABLES = [
        'employee_resignation_handover_items' => 'erhi_task_fk',
        'employee_termination_handover_items' => 'ethi_task_fk',
    ];

    public function up(): void
    {
        foreach (self::HANDOVER_TABLES as $table => $foreignKey) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'task_id')) {
                continue;
            }

            if (! Schema::hasColumn($table, 'legacy_task_id')) {
                Schema::table($table, fn (Blueprint $blueprint) =>
                    $blueprint->unsignedBigInteger('legacy_task_id')->nullable()
                );
            }

            DB::table($table)->whereNotNull('task_id')->whereNull('legacy_task_id')
                ->update(['legacy_task_id' => DB::raw('task_id')]);

            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropForeign($foreignKey));
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('task_id'));
        }

        foreach (self::TABLES as $table) {
            $archive = 'archive_' . $table;

            if (Schema::hasTable($table) && Schema::hasTable($archive)) {
                throw new \RuntimeException("Tabel {$table} dan {$archive} sama-sama ada; periksa sebelum migrasi.");
            }

            if (Schema::hasTable($table)) {
                Schema::rename($table, $archive);
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::TABLES) as $table) {
            $archive = 'archive_' . $table;

            if (Schema::hasTable($archive) && ! Schema::hasTable($table)) {
                Schema::rename($archive, $table);
            }
        }

        foreach (self::HANDOVER_TABLES as $table => $foreignKey) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'legacy_task_id')) {
                continue;
            }

            if (! Schema::hasColumn($table, 'task_id')) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->unsignedBigInteger('task_id')->nullable());
            }

            DB::table($table)->whereNotNull('legacy_task_id')
                ->update(['task_id' => DB::raw('legacy_task_id')]);

            Schema::table($table, fn (Blueprint $blueprint) =>
                $blueprint->foreign('task_id', $foreignKey)->references('id')->on('tasks')->nullOnDelete()
            );
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('legacy_task_id'));
        }
    }
};
