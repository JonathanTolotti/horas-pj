<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('standard_day_periods')->nullable()->after('billing_cycle_day');
            $table->unsignedBigInteger('standard_day_project_id')->nullable()->after('standard_day_periods');
            $table->string('standard_day_description', 500)->nullable()->after('standard_day_project_id');

            $table->foreign('standard_day_project_id')
                ->references('id')->on('projects')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['standard_day_project_id']);
            $table->dropColumn(['standard_day_periods', 'standard_day_project_id', 'standard_day_description']);
        });
    }
};
