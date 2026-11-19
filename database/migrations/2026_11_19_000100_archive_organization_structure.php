<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Preserve organization history without keeping any active HRIS relation.
     * The earlier work-management archive may still have foreign keys pointing
     * at these tables; database table renaming maintains those references.
     */
    public function up(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'team_id')) {
            if (! Schema::hasColumn('employees', 'legacy_team_id')) {
                Schema::table('employees', function (Blueprint $table): void {
                    $table->unsignedBigInteger('legacy_team_id')->nullable();
                });
            }

            DB::table('employees')->whereNotNull('team_id')->whereNull('legacy_team_id')
                ->update(['legacy_team_id' => DB::raw('team_id')]);

            Schema::table('employees', function (Blueprint $table): void {
                $table->dropForeign(['team_id']);
            });
            Schema::table('employees', function (Blueprint $table): void {
                $table->dropColumn('team_id');
            });
        }

        foreach (['teams', 'divisis'] as $table) {
            $archive = 'archive_' . $table;
            if (Schema::hasTable($table) && Schema::hasTable($archive)) {
                throw new RuntimeException("Tabel {$table} dan {$archive} keduanya ada; migrasi dihentikan.");
            }

            if (Schema::hasTable($table)) {
                Schema::rename($table, $archive);
            }
        }
    }

    public function down(): void
    {
        foreach (['divisis', 'teams'] as $table) {
            if (Schema::hasTable('archive_' . $table) && ! Schema::hasTable($table)) {
                Schema::rename('archive_' . $table, $table);
            }
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'legacy_team_id')) {
            if (! Schema::hasColumn('employees', 'team_id')) {
                Schema::table('employees', function (Blueprint $table): void {
                    $table->unsignedBigInteger('team_id')->nullable();
                });
            }

            DB::table('employees')->whereNotNull('legacy_team_id')
                ->update(['team_id' => DB::raw('legacy_team_id')]);

            Schema::table('employees', function (Blueprint $table): void {
                $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
            });
            Schema::table('employees', function (Blueprint $table): void {
                $table->dropColumn('legacy_team_id');
            });
        }
    }
};
