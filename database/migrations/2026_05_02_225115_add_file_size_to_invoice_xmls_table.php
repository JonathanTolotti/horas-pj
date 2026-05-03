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
            $table->unsignedBigInteger('xml_file_size')->nullable()->after('path');
            $table->unsignedBigInteger('danfse_file_size')->nullable()->after('danfse_path');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_xmls', function (Blueprint $table) {
            $table->dropColumn(['xml_file_size', 'danfse_file_size']);
        });
    }
};
