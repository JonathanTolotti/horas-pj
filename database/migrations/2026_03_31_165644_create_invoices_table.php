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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title', 200);
            $table->string('reference_month', 7); // YYYY-MM
            $table->enum('status', ['rascunho', 'aberta', 'conciliada', 'encerrada', 'cancelada'])->default('rascunho');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'reference_month']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
