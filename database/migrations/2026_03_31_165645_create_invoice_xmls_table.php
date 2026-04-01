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
        Schema::create('invoice_xmls', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('filename', 255);
            $table->string('path', 500);
            $table->string('invoice_number', 50)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('issued_at')->nullable();
            $table->string('provider_cnpj', 18)->nullable();
            $table->string('recipient_cnpj', 18)->nullable();
            $table->string('provider_name', 200)->nullable();
            $table->string('recipient_name', 200)->nullable();
            $table->boolean('xml_parsed')->default(false);
            $table->text('parse_error')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_xmls');
    }
};
