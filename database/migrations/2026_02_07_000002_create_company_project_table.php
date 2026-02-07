<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->decimal('percentage', 5, 2);
            $table->timestamps();

            $table->unique(['company_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_project');
    }
};
