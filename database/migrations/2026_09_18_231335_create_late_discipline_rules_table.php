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
        Schema::create('late_discipline_rules', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            /*
            |--------------------------------------------------------------------------
            | THRESHOLD
            |--------------------------------------------------------------------------
            | Jumlah keterlambatan yang menjadi dasar tindakan.
            |
            | Contoh:
            | 3 kali → Rp20.000
            | 6 kali → Rp40.000
            | 9 kali → Rp60.000
            |
            */
            $table->unsignedInteger('threshold')->default(3);

            /*
            |--------------------------------------------------------------------------
            | PERIOD
            |--------------------------------------------------------------------------
            | Saat ini aturan menggunakan periode bulanan.
            | Menggunakan string agar nantinya mudah dikembangkan.
            |
            */
            $table->string('period_type', 50)->default('monthly');

            /*
            |--------------------------------------------------------------------------
            | ACTION
            |--------------------------------------------------------------------------
            | Contoh saat ini:
            | payroll_deduction
            |
            */
            $table->string('action_type', 50);

            /*
            |--------------------------------------------------------------------------
            | ACTION AMOUNT
            |--------------------------------------------------------------------------
            | Nominal tindakan.
            |
            | Contoh:
            | threshold = 3
            | action_amount = 20000
            |
            */
            $table->decimal('action_amount', 15, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */
            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('late_discipline_rules');
    }
};
