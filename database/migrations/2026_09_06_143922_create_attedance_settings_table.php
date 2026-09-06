<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attedance_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('late_tolerance_minutes')->default(5);
            $table->boolean('require_location')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attedance_settings');
    }
};
