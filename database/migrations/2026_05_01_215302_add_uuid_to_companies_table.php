<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('uuid', 36)->nullable()->after('id');
        });

        // Preenche empresas existentes
        DB::table('companies')->whereNull('uuid')->orderBy('id')->each(function ($company) {
            DB::table('companies')->where('id', $company->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('uuid', 36)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
