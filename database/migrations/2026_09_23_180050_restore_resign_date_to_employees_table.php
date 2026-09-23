<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('employees', 'ResignDate')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->date('ResignDate')->nullable()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employees', 'ResignDate')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('ResignDate');
            });
        }
    }
};
