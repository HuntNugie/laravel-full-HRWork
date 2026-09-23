<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_resignation_clearances', function (Blueprint $table) {
            $table->json('action_state')->nullable()->after('status');
        });

        Schema::table('employee_termination_clearances', function (Blueprint $table) {
            $table->json('action_state')->nullable()->after('status');
        });

        Schema::table('employee_resignation_handover_items', function (Blueprint $table) {
            $table->json('action_state')->nullable()->after('status');
        });

        Schema::table('employee_termination_handover_items', function (Blueprint $table) {
            $table->json('action_state')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('employee_termination_handover_items', function (Blueprint $table) {
            $table->dropColumn('action_state');
        });

        Schema::table('employee_resignation_handover_items', function (Blueprint $table) {
            $table->dropColumn('action_state');
        });

        Schema::table('employee_termination_clearances', function (Blueprint $table) {
            $table->dropColumn('action_state');
        });

        Schema::table('employee_resignation_clearances', function (Blueprint $table) {
            $table->dropColumn('action_state');
        });
    }
};
