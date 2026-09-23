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
            return;
        }

        if (! Schema::hasColumn('project_reports', 'progress')) {
            Schema::table('project_reports', function (Blueprint $table) {
                $table->unsignedTinyInteger('progress')->nullable()->after('content');
            });
        }

    }

    public function down(): void
    {
        if (Schema::hasTable('project_reports') && Schema::hasColumn('project_reports', 'progress')) {
            Schema::table('project_reports', function (Blueprint $table) {
                $table->dropColumn('progress');
            });
        }
    }
};
