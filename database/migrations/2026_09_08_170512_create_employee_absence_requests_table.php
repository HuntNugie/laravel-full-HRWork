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
        Schema::create('employee_absence_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained("employees")->cascadeOnDelete();
            $table->date("date");
            $table->enum("type", ['sakit', 'izin']);
            $table->text("reason")->nullable();
            $table->enum("status", ["pending", "approved", "rejected"])->default('pending');
            $table->foreignId("approved_by")->nullable()->constrained("users")->cascadeOnDelete();
            $table->datetime("approved_at")->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_absence_requests');
    }
};
