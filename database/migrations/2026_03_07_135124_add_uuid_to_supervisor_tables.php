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
        Schema::table('supervisor_invitations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        Schema::table('supervisor_accesses', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('supervisor_invitations', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('supervisor_accesses', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
