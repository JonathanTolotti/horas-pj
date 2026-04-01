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
        Schema::table('invoice_entries', function (Blueprint $table) {
            $table->foreignId('time_entry_id')->nullable()->constrained('time_entries')->onDelete('set null')->after('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_entries', function (Blueprint $table) {
            $table->dropForeign(['time_entry_id']);
            $table->dropColumn('time_entry_id');
        });
    }
};
