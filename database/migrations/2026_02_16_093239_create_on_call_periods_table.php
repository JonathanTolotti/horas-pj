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
        Schema::create('on_call_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->datetime('start_datetime');
            $table->datetime('end_datetime');
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('total_hours', 8, 2);
            $table->decimal('worked_hours', 8, 2)->default(0);
            $table->decimal('on_call_hours', 8, 2);
            $table->string('month_reference', 7);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'month_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('on_call_periods');
    }

};
