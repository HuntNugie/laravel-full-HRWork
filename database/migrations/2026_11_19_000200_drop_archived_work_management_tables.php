<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Delete retired Work Management tables in child-before-parent order.
     * Historical data in these tables is intentionally discarded.
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

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table)) {
                throw new RuntimeException(
                    "Tabel {$table} masih aktif. Jalankan/periksa migrasi pengarsipan sebelum menghapusnya."
                );
            }
        }

        foreach (self::TABLES as $table) {
            Schema::dropIfExists('archive_' . $table);
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Penghapusan data Work Management bersifat permanen; rollback tidak dapat memulihkan data yang dihapus.'
        );
    }
};
