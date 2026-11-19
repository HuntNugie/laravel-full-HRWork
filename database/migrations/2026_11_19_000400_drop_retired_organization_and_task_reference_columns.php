<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = [
        'employees' => 'legacy_team_id',
        'employee_resignation_handover_items' => 'legacy_task_id',
        'employee_termination_handover_items' => 'legacy_task_id',
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn($column));
            }
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Kolom referensi historis sudah dihapus permanen; rollback tidak dapat memulihkan nilainya.'
        );
    }
};
