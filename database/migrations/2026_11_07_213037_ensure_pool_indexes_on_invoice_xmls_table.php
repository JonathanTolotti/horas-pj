<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_xmls', function (Blueprint $table) {
            if (! Schema::hasIndex('invoice_xmls', 'invoice_xmls_user_id_access_key_unique')) {
                $table->unique(['user_id', 'access_key']);
            }
            if (! Schema::hasIndex('invoice_xmls', 'invoice_xmls_user_id_competence_index')) {
                $table->index(['user_id', 'competence']);
            }
        });
    }

    public function down(): void
    {
        // Intencionalmente vazio: os índices pertencem à migration anterior.
    }
};
