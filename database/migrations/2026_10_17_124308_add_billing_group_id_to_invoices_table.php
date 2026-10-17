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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('billing_group_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['user_id', 'reference_month', 'billing_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'reference_month', 'billing_group_id']);
            $table->dropConstrainedForeignId('billing_group_id');
        });
    }
};
