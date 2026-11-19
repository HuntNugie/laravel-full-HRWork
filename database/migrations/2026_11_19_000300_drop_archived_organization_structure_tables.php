<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run after the Work Management purge: archived projects reference these tables.
     */
    public function up(): void
    {
        foreach (['teams', 'divisis'] as $table) {
            if (Schema::hasTable($table)) {
                throw new RuntimeException(
                    "Tabel {$table} masih aktif. Jalankan/periksa migrasi pengarsipan struktur organisasi."
                );
            }
        }

        Schema::dropIfExists('archive_teams');
        Schema::dropIfExists('archive_divisis');
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Penghapusan data struktur organisasi bersifat permanen; rollback tidak dapat memulihkan data yang dihapus.'
        );
    }
};
