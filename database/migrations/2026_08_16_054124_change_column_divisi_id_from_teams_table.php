<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('divisi_id')
                ->nullable()
                ->change();

            $table->foreign('divisi_id')
                ->references('id')
                ->on('divisis')
                ->nullOnDelete();
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