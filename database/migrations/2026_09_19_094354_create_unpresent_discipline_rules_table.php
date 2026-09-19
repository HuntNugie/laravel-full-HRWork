<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unpresent_discipline_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('threshold')->default(3);
            $table->string('period_type', 50)->default('monthly');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unpresent_discipline_rules');
    }
};
