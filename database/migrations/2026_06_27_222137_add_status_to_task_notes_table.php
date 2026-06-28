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
        Schema::table('task_notes', function (Blueprint $table) {
            $table->enum('status', ['pending', 'done'])->default('pending')->after('minutes');
        });
    }

    public function down(): void
    {
        Schema::table('task_notes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
