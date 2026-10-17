<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_xmls', function (Blueprint $table) {
            // Notas do pool podem existir sem fatura (aguardando processamento ou com erro)
            $table->foreignId('invoice_id')->nullable()->change();

            $table->foreignId('user_id')->nullable()->after('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->string('status', 20)->default('processed')->after('company_id');
            $table->string('access_key', 60)->nullable()->after('status');
            $table->string('competence', 7)->nullable()->after('access_key');
            $table->string('series', 10)->nullable()->after('invoice_number');
            $table->decimal('gross_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('iss_amount', 12, 2)->nullable()->after('gross_amount');
            $table->boolean('iss_retained')->nullable()->after('iss_amount');
            $table->string('service_code', 20)->nullable();
            $table->text('service_description')->nullable();
            $table->string('municipality_code', 10)->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->dateTime('processed_at')->nullable();

            $table->unique(['user_id', 'access_key']);
            $table->index(['user_id', 'competence']);
            $table->index(['user_id', 'status']);
        });

        // Backfill: notas existentes pertencem ao dono da fatura
        DB::table('invoice_xmls')->whereNull('user_id')->update([
            'user_id' => DB::raw('(select user_id from invoices where invoices.id = invoice_xmls.invoice_id)'),
        ]);

        DB::table('invoice_xmls')->whereNull('competence')->whereNotNull('issued_at')->update([
            'competence' => DB::raw('substr(issued_at, 1, 7)'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_xmls', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'access_key']);
            $table->dropIndex(['user_id', 'competence']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn([
                'status', 'access_key', 'competence', 'series', 'gross_amount', 'iss_amount',
                'iss_retained', 'service_code', 'service_description', 'municipality_code',
                'attempts', 'processed_at',
            ]);
        });
    }
};
