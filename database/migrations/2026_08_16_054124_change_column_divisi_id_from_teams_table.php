<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
           $table->dropForeign(['divisi_id']);
           $table->dropColumn('divisi_id');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('divisi_id')->nullable()->constrained('divisis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['divisi_id']);

            $table->unsignedBigInteger('divisi_id')
                ->nullable(false)
                ->change();
        });
    }
};
