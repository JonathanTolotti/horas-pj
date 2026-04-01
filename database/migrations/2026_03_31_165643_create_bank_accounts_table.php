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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_name', 100);
            $table->string('branch', 20);
            $table->string('account_number', 30);
            $table->enum('account_type', ['corrente', 'poupança'])->default('corrente');
            $table->string('holder_name', 150);
            $table->string('pix_key', 150)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
