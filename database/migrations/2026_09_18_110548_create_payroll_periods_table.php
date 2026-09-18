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
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->date('start_date');
            $table->date('end_date');

            $table->date('payment_date')->nullable();

            $table->enum('status', [
                'draft',
                'processing',
                'processed',
                'paid',
                'cancelled',
            ])->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('processed_at')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['start_date', 'end_date'],
                'payroll_period_date_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
