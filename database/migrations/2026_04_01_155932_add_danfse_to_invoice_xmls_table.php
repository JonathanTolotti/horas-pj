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
        Schema::table('invoice_xmls', function (Blueprint $table) {
            $table->string('danfse_filename', 255)->nullable()->after('parse_error');
            $table->string('danfse_path', 500)->nullable()->after('danfse_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_xmls', function (Blueprint $table) {
            $table->dropColumn(['danfse_filename', 'danfse_path']);
        });
    }
};
