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
        Schema::create('monthly_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('month_reference', 7); // formato YYYY-MM
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('extra_value', 10, 2)->default(0);
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'month_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_adjustments');
    }
};
