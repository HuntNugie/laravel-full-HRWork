<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {

            $table->id();

            // =====================================================
            // RELATIONSHIP
            // =====================================================

            $table->foreignId('payroll_id')
                ->constrained('payrolls')
                ->cascadeOnDelete();


            // =====================================================
            // ITEM INFORMATION
            // =====================================================

            $table->string('name');

            $table->enum('type', [
                'earning',
                'deduction',
            ]);

            $table->string('category')->nullable();


            // =====================================================
            // CALCULATION
            // =====================================================

            $table->decimal('amount', 15, 2)->default(0);

            $table->decimal('quantity', 15, 2)->nullable();

            $table->decimal('rate', 15, 2)->nullable();


            // =====================================================
            // SOURCE
            // =====================================================

            $table->enum('source', [
                'system',
                'manual',
            ])->default('system');


            // =====================================================
            // ADDITIONAL INFORMATION
            // =====================================================

            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
