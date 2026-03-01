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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('razao_social')->nullable()->after('cnpj');
            $table->string('email')->nullable()->after('razao_social');
            $table->string('telefone', 20)->nullable()->after('email');
            $table->string('cep', 9)->nullable()->after('telefone');
            $table->string('logradouro')->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('logradouro');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('uf', 2)->nullable()->after('cidade');
            $table->string('inscricao_municipal')->nullable()->after('uf');
            $table->string('inscricao_estadual')->nullable()->after('inscricao_municipal');
            $table->string('responsavel_nome')->nullable()->after('inscricao_estadual');
            $table->string('responsavel_email')->nullable()->after('responsavel_nome');
            $table->string('responsavel_telefone', 20)->nullable()->after('responsavel_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'razao_social', 'email', 'telefone', 'cep', 'logradouro',
                'numero', 'complemento', 'bairro', 'cidade', 'uf',
                'inscricao_municipal', 'inscricao_estadual',
                'responsavel_nome', 'responsavel_email', 'responsavel_telefone',
            ]);
        });
    }
};
