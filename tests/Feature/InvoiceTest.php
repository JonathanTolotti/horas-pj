<?php

use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceEntry;
use App\Models\InvoiceXml;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    Setting::create([
        'user_id'        => $this->user->id,
        'hourly_rate'    => 100,
        'extra_value'    => 0,
        'discount_value' => 0,
    ]);

    Subscription::create([
        'user_id'   => $this->user->id,
        'plan'      => 'premium',
        'status'    => 'active',
        'starts_at' => now()->subDay(),
        'ends_at'   => now()->addYear(),
    ]);
});

// ─── BankAccount CRUD ────────────────────────────────────────────────────────

describe('BankAccount CRUD', function () {

    test('cria conta bancaria', function () {
        $response = $this->actingAs($this->user)->postJson('/bank-accounts', [
            'bank_name'      => 'Banco do Brasil',
            'branch'         => '1234-5',
            'account_number' => '12345-6',
            'account_type'   => 'corrente',
            'holder_name'    => 'João PJ',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('bank_accounts', ['bank_name' => 'Banco do Brasil', 'user_id' => $this->user->id]);
    });

    test('conta bancaria tem uuid', function () {
        $this->actingAs($this->user)->postJson('/bank-accounts', [
            'bank_name'      => 'Nubank',
            'branch'         => '0001',
            'account_number' => '99999-0',
            'account_type'   => 'corrente',
            'holder_name'    => 'João PJ',
        ]);

        $account = BankAccount::where('user_id', $this->user->id)->first();
        expect($account->uuid)->not->toBeNull();
    });

    test('atualiza conta bancaria via uuid', function () {
        $account = BankAccount::create([
            'user_id'        => $this->user->id,
            'bank_name'      => 'Antigo Banco',
            'branch'         => '001',
            'account_number' => '1111',
            'account_type'   => 'corrente',
            'holder_name'    => 'João',
        ]);

        $response = $this->actingAs($this->user)->putJson("/bank-accounts/{$account->uuid}", [
            'bank_name'      => 'Novo Banco',
            'branch'         => '002',
            'account_number' => '2222',
            'account_type'   => 'poupança',
            'holder_name'    => 'João PJ',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('bank_accounts', ['bank_name' => 'Novo Banco', 'id' => $account->id]);
    });

    test('exclui conta bancaria sem faturas', function () {
        $account = BankAccount::create([
            'user_id'        => $this->user->id,
            'bank_name'      => 'Banco X',
            'branch'         => '001',
            'account_number' => '1111',
            'account_type'   => 'corrente',
            'holder_name'    => 'João',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/bank-accounts/{$account->uuid}");

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    });

    test('nao exclui conta bancaria com faturas vinculadas', function () {
        $account = BankAccount::create([
            'user_id'        => $this->user->id,
            'bank_name'      => 'Banco Y',
            'branch'         => '001',
            'account_number' => '1111',
            'account_type'   => 'corrente',
            'holder_name'    => 'João',
        ]);

        Invoice::create([
            'user_id'         => $this->user->id,
            'bank_account_id' => $account->id,
            'title'           => 'Fatura Teste',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/bank-accounts/{$account->uuid}");

        $response->assertStatus(422)->assertJson(['success' => false]);
        $this->assertDatabaseHas('bank_accounts', ['id' => $account->id]);
    });

    test('toggle ativa desativa conta bancaria', function () {
        $account = BankAccount::create([
            'user_id'        => $this->user->id,
            'bank_name'      => 'Banco Z',
            'branch'         => '001',
            'account_number' => '1111',
            'account_type'   => 'corrente',
            'holder_name'    => 'João',
            'active'         => true,
        ]);

        $this->actingAs($this->user)->patchJson("/bank-accounts/{$account->uuid}/toggle")
            ->assertJson(['success' => true]);

        expect($account->fresh()->active)->toBeFalse();
    });

    test('usuario free nao acessa bank accounts', function () {
        $freeUser = User::factory()->create();
        Setting::create(['user_id' => $freeUser->id, 'hourly_rate' => 100, 'extra_value' => 0, 'discount_value' => 0]);

        // Requisição web normal → redireciona para tela de planos
        $this->actingAs($freeUser)->get('/bank-accounts')
            ->assertStatus(302);
    });
});

// ─── Invoice CRUD ─────────────────────────────────────────────────────────────

describe('Invoice CRUD', function () {

    test('cria fatura como rascunho', function () {
        $response = $this->actingAs($this->user)->postJson('/invoices', [
            'title'           => 'Serviços de TI – Março 2026',
            'reference_month' => '2026-03',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('invoices', [
            'title'   => 'Serviços de TI – Março 2026',
            'status'  => 'rascunho',
            'user_id' => $this->user->id,
        ]);
    });

    test('fatura tem uuid', function () {
        $this->actingAs($this->user)->postJson('/invoices', [
            'title'           => 'Fatura UUID',
            'reference_month' => '2026-03',
        ]);

        $invoice = Invoice::where('user_id', $this->user->id)->first();
        expect($invoice->uuid)->not->toBeNull();
    });

    test('atualiza fatura em rascunho', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Título Antigo',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $response = $this->actingAs($this->user)->putJson("/invoices/{$invoice->uuid}", [
            'title'           => 'Título Novo',
            'reference_month' => '2026-03',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('invoices', ['title' => 'Título Novo', 'id' => $invoice->id]);
    });

    test('nao atualiza fatura encerrada', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura Encerrada',
            'reference_month' => '2026-03',
            'status'          => 'encerrada',
        ]);

        $this->actingAs($this->user)->putJson("/invoices/{$invoice->uuid}", [
            'title'           => 'Tentativa de Alterar',
            'reference_month' => '2026-03',
        ])->assertStatus(403)->assertJson(['success' => false]);
    });

    test('exclui fatura em rascunho', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Para Excluir',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $this->actingAs($this->user)->deleteJson("/invoices/{$invoice->uuid}")
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    });

    test('nao exclui fatura aberta', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura Aberta',
            'reference_month' => '2026-03',
            'status'          => 'aberta',
        ]);

        $this->actingAs($this->user)->deleteJson("/invoices/{$invoice->uuid}")
            ->assertStatus(422)->assertJson(['success' => false]);
    });

    test('muda status da fatura', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura Status',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $this->actingAs($this->user)->patchJson("/invoices/{$invoice->uuid}/status", ['status' => 'aberta'])
            ->assertJson(['success' => true]);

        expect($invoice->fresh()->status)->toBe('aberta');
    });

    test('nao muda status de fatura encerrada', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura Fechada',
            'reference_month' => '2026-03',
            'status'          => 'encerrada',
        ]);

        $this->actingAs($this->user)->patchJson("/invoices/{$invoice->uuid}/status", ['status' => 'aberta'])
            ->assertStatus(403)->assertJson(['success' => false]);
    });

    test('nao acessa fatura de outro usuario', function () {
        $other = User::factory()->create();
        Setting::create(['user_id' => $other->id, 'hourly_rate' => 100, 'extra_value' => 0, 'discount_value' => 0]);

        $invoice = Invoice::create([
            'user_id'         => $other->id,
            'title'           => 'Fatura Privada',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $this->actingAs($this->user)->get("/invoices/{$invoice->uuid}")
            ->assertStatus(404);
    });
});

// ─── InvoiceEntry ─────────────────────────────────────────────────────────────

describe('InvoiceEntry', function () {

    test('adiciona lancamento de credito', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $response = $this->actingAs($this->user)->postJson("/invoices/{$invoice->uuid}/entries", [
            'type'        => 'credit',
            'description' => 'Desenvolvimento',
            'amount'      => 5000.00,
            'date'        => '2026-03-01',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('invoice_entries', ['invoice_id' => $invoice->id, 'type' => 'credit', 'amount' => 5000]);
    });

    test('lancamento tem uuid', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $this->actingAs($this->user)->postJson("/invoices/{$invoice->uuid}/entries", [
            'type'        => 'credit',
            'description' => 'Teste UUID',
            'amount'      => 100,
            'date'        => '2026-03-01',
        ]);

        $entry = InvoiceEntry::where('invoice_id', $invoice->id)->first();
        expect($entry->uuid)->not->toBeNull();
    });

    test('calcula totais apos adicionar lancamentos', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura Totais',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $this->actingAs($this->user)->postJson("/invoices/{$invoice->uuid}/entries", [
            'type' => 'credit', 'description' => 'Crédito', 'amount' => 3000, 'date' => '2026-03-01',
        ]);
        $this->actingAs($this->user)->postJson("/invoices/{$invoice->uuid}/entries", [
            'type' => 'debit', 'description' => 'Débito', 'amount' => 500, 'date' => '2026-03-01',
        ]);

        $invoice->refresh();
        expect($invoice->getTotalCredits())->toBe(3000.0);
        expect($invoice->getTotalDebits())->toBe(500.0);
        expect($invoice->getNetTotal())->toBe(2500.0);
    });

    test('remove lancamento', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $entry = InvoiceEntry::create([
            'invoice_id'  => $invoice->id,
            'type'        => 'credit',
            'description' => 'Para Remover',
            'amount'      => 100,
            'date'        => '2026-03-01',
        ]);

        $this->actingAs($this->user)->deleteJson("/invoices/{$invoice->uuid}/entries/{$entry->uuid}")
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('invoice_entries', ['id' => $entry->id]);
    });

    test('nao adiciona lancamento em fatura encerrada', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura Encerrada',
            'reference_month' => '2026-03',
            'status'          => 'encerrada',
        ]);

        $this->actingAs($this->user)->postJson("/invoices/{$invoice->uuid}/entries", [
            'type' => 'credit', 'description' => 'Tentativa', 'amount' => 100, 'date' => '2026-03-01',
        ])->assertStatus(403)->assertJson(['success' => false]);
    });
});

// ─── Conciliação ──────────────────────────────────────────────────────────────

describe('Conciliação', function () {

    test('status pendente sem xml', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        InvoiceEntry::create([
            'invoice_id'         => $invoice->id,
            'type'               => 'credit',
            'description'        => 'Serviço',
            'amount'             => 5000,
            'date'               => '2026-03-01',
            'reconcile_with_xml' => true,
        ]);

        expect($invoice->getReconciliationStatus())->toBe('pendente');
    });

    test('status conciliado quando valores batem', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        InvoiceEntry::create([
            'invoice_id'         => $invoice->id,
            'type'               => 'credit',
            'description'        => 'Serviço',
            'amount'             => 5000,
            'date'               => '2026-03-01',
            'reconcile_with_xml' => true,
        ]);

        InvoiceXml::create([
            'invoice_id' => $invoice->id,
            'filename'   => 'nf.xml',
            'path'       => 'invoices/test/nf.xml',
            'amount'     => 5000,
            'xml_parsed' => true,
        ]);

        expect($invoice->getReconciliationStatus())->toBe('conciliado');
    });

    test('status parcial quando valores divergem', function () {
        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        InvoiceEntry::create([
            'invoice_id'         => $invoice->id,
            'type'               => 'credit',
            'description'        => 'Serviço',
            'amount'             => 5000,
            'date'               => '2026-03-01',
            'reconcile_with_xml' => true,
        ]);

        InvoiceXml::create([
            'invoice_id' => $invoice->id,
            'filename'   => 'nf.xml',
            'path'       => 'invoices/test/nf.xml',
            'amount'     => 3000,
            'xml_parsed' => true,
        ]);

        expect($invoice->getReconciliationStatus())->toBe('parcial');
    });
});

// ─── InvoiceXml upload ────────────────────────────────────────────────────────

describe('InvoiceXml', function () {

    test('faz upload de xml e armazena arquivo', function () {
        Storage::fake('local');

        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura XML',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $xmlContent = '<?xml version="1.0"?><root><valor>100</valor></root>';
        $file = UploadedFile::fake()->createWithContent('nota.xml', $xmlContent);

        $response = $this->actingAs($this->user)->post("/invoices/{$invoice->uuid}/xmls", [
            'xml' => $file,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('invoice_xmls', ['invoice_id' => $invoice->id, 'filename' => 'nota.xml']);
    });

    test('xml tem uuid', function () {
        Storage::fake('local');

        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura UUID XML',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $file = UploadedFile::fake()->createWithContent('uuid.xml', '<?xml version="1.0"?><root/>');

        $this->actingAs($this->user)->post("/invoices/{$invoice->uuid}/xmls", ['xml' => $file]);

        $xml = InvoiceXml::where('invoice_id', $invoice->id)->first();
        expect($xml->uuid)->not->toBeNull();
    });

    test('nao faz upload de xml em fatura encerrada', function () {
        Storage::fake('local');

        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura Encerrada',
            'reference_month' => '2026-03',
            'status'          => 'encerrada',
        ]);

        $file = UploadedFile::fake()->createWithContent('nota.xml', '<?xml version="1.0"?><root/>');

        $this->actingAs($this->user)->post("/invoices/{$invoice->uuid}/xmls", ['xml' => $file])
            ->assertStatus(403)->assertJson(['success' => false]);
    });

    test('exclui xml e arquivo do storage', function () {
        Storage::fake('local');

        $invoice = Invoice::create([
            'user_id'         => $this->user->id,
            'title'           => 'Fatura',
            'reference_month' => '2026-03',
            'status'          => 'rascunho',
        ]);

        $storagePath = 'invoices/' . $this->user->id . '/' . $invoice->id . '/nota.xml';
        Storage::put($storagePath, '<?xml version="1.0"?><root/>');

        $xml = InvoiceXml::create([
            'invoice_id' => $invoice->id,
            'filename'   => 'nota.xml',
            'path'       => $storagePath,
            'xml_parsed' => false,
        ]);

        $this->actingAs($this->user)->deleteJson("/invoices/{$invoice->uuid}/xmls/{$xml->uuid}")
            ->assertJson(['success' => true]);

        Storage::assertMissing($storagePath);
        $this->assertDatabaseMissing('invoice_xmls', ['id' => $xml->id]);
    });
});

// ─── Premium Gate ─────────────────────────────────────────────────────────────

describe('Premium Gate', function () {

    test('usuario free e redirecionado ao tentar criar fatura', function () {
        $freeUser = User::factory()->create();
        Setting::create(['user_id' => $freeUser->id, 'hourly_rate' => 100, 'extra_value' => 0, 'discount_value' => 0]);

        // Requisição web normal → redireciona para tela de planos
        $this->actingAs($freeUser)->post('/invoices', [
            'title'           => 'Fatura Free',
            'reference_month' => '2026-03',
        ])->assertStatus(302);
    });

    test('usuario free nao acessa lista de faturas', function () {
        $freeUser = User::factory()->create();
        Setting::create(['user_id' => $freeUser->id, 'hourly_rate' => 100, 'extra_value' => 0, 'discount_value' => 0]);

        $this->actingAs($freeUser)->get('/invoices')
            ->assertStatus(302);
    });
});
