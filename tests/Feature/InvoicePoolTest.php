<?php

use App\Jobs\ProcessInvoiceXml;
use App\Models\BillingGroup;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceXml;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Services\DanfseService;
use App\Services\XmlParserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Gera um XML de NFS-e Nacional mínimo com os campos usados pelo parser.
 */
function nfseXml(string $key, string $tomaCnpj, string $competence = '2026-03', float $vServ = 1000.00, float $vLiq = 950.00): string
{
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<NFSe xmlns="http://www.sped.fazenda.gov.br/nfse">
  <infNFSe Id="NFS{$key}">
    <nNFSe>42</nNFSe>
    <emit><CNPJ>11222333000181</CNPJ><xNome>Prestador PJ</xNome></emit>
    <valores><vLiq>{$vLiq}</vLiq></valores>
    <DPS><infDPS>
      <dhEmi>{$competence}-10T10:00:00-03:00</dhEmi>
      <serie>900</serie>
      <dCompet>{$competence}-01</dCompet>
      <toma><CNPJ>{$tomaCnpj}</CNPJ><xNome>Cliente</xNome></toma>
      <serv><cServ><cTribNac>010701</cTribNac><xDescServ>Desenvolvimento de software</xDescServ></cServ></serv>
      <valores><vServPrest><vServ>{$vServ}</vServ></vServPrest>
        <trib><tribMun><tpRetISSQN>1</tpRetISSQN></tribMun></trib></valores>
    </infDPS></DPS>
  </infNFSe>
</NFSe>
XML;
}

function nfseFile(string $key, string $tomaCnpj, string $competence = '2026-03', float $vServ = 1000.00): UploadedFile
{
    return UploadedFile::fake()->createWithContent("nota-{$key}.xml", nfseXml($key, $tomaCnpj, $competence, $vServ, $vServ));
}

function poolKey(int $n): string
{
    return str_pad((string) $n, 50, '0', STR_PAD_LEFT);
}

beforeEach(function () {
    Storage::fake();

    $this->user = User::factory()->create(['storage_quota' => 50 * 1024 * 1024, 'storage_used' => 0]);
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

    $this->companyA = Company::create(['user_id' => $this->user->id, 'name' => 'Cliente A', 'cnpj' => '06.367.953/0001-79', 'active' => true]);
    $this->companyB = Company::create(['user_id' => $this->user->id, 'name' => 'Cliente B', 'cnpj' => '27.270.649/0001-89', 'active' => true]);
});

describe('Parser', function () {
    test('extrai chave, competência e valores da NFS-e Nacional', function () {
        Storage::put('t.xml', nfseXml(poolKey(1), '06367953000179', '2026-04', 1200, 1100));

        $parsed = app(XmlParserService::class)->parse('t.xml');

        expect($parsed['xml_parsed'])->toBeTrue()
            ->and($parsed['access_key'])->toBe(poolKey(1))
            ->and($parsed['competence'])->toBe('2026-04')
            ->and($parsed['gross_amount'])->toBe(1200.0)
            ->and($parsed['amount'])->toBe(1100.0)
            ->and($parsed['iss_retained'])->toBeFalse()
            ->and($parsed['service_code'])->toBe('010701')
            ->and($parsed['recipient_cnpj'])->toBe('06.367.953/0001-79');
    });

    test('rejeita XML com DOCTYPE', function () {
        Storage::put('x.xml', '<?xml version="1.0"?><!DOCTYPE a [<!ENTITY x SYSTEM "file:///etc/passwd">]><a>&x;</a>');

        $parsed = app(XmlParserService::class)->parse('x.xml');

        expect($parsed['xml_parsed'])->toBeFalse();
    });
});

describe('Upload no pool', function () {
    test('upload cria notas aguardando processamento e enfileira o job', function () {
        Queue::fake();

        $response = $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179'), nfseFile(poolKey(2), '27270649000189')],
        ], ['Accept' => 'application/json']);

        $response->assertOk()->assertJson(['success' => true]);
        expect(InvoiceXml::forUser($this->user->id)->where('status', 'pending')->count())->toBe(2);
        Queue::assertPushed(ProcessInvoiceXml::class, 2);
    });

    test('arquivo é salvo com uuid no caminho, não com o nome original', function () {
        Queue::fake();

        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json'])->assertOk();

        $xml = InvoiceXml::forUser($this->user->id)->first();
        expect($xml->path)->toBe("invoices/{$this->user->id}/pool/{$xml->uuid}.xml");
        Storage::assertExists($xml->path);
    });

    test('bloqueia upload acima da cota', function () {
        $this->user->update(['storage_quota' => 10, 'storage_used' => 0]);

        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json'])->assertStatus(422);

        expect(InvoiceXml::count())->toBe(0);
    });

    test('usuário free não acessa o pool', function () {
        $free = User::factory()->create();

        $this->actingAs($free)->get('/invoice-pool')->assertRedirect(route('subscription.plans'));
        $this->actingAs($free)->postJson('/invoice-pool', [])->assertStatus(403);
    });
});

describe('Processamento e vínculo', function () {
    test('cria fatura quando não existe na competência', function () {
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json'])->assertOk();

        $xml = InvoiceXml::forUser($this->user->id)->first();
        expect($xml->status)->toBe('processed')
            ->and($xml->competence)->toBe('2026-03')
            ->and($xml->invoice->reference_month)->toBe('2026-03')
            ->and($xml->invoice->company_id)->toBe($this->companyA->id)
            ->and($xml->invoice->status)->toBe(Invoice::STATUS_DRAFT);
    });

    test('vincula à fatura existente da competência', function () {
        $invoice = Invoice::create([
            'user_id' => $this->user->id, 'company_id' => $this->companyA->id,
            'title' => 'Março', 'reference_month' => '2026-03', 'status' => Invoice::STATUS_OPEN,
        ]);

        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json'])->assertOk();

        expect(InvoiceXml::first()->invoice_id)->toBe($invoice->id)
            ->and(Invoice::count())->toBe(1);
    });

    test('CNPJs do mesmo grupo de faturamento entram na mesma fatura', function () {
        $group = BillingGroup::create(['user_id' => $this->user->id, 'name' => 'Grupo', 'primary_company_id' => $this->companyB->id]);
        $this->companyA->update(['billing_group_id' => $group->id]);
        $this->companyB->update(['billing_group_id' => $group->id]);

        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179'), nfseFile(poolKey(2), '27270649000189')],
        ], ['Accept' => 'application/json'])->assertOk();

        expect(Invoice::count())->toBe(1);
        $invoice = Invoice::first();
        expect($invoice->billing_group_id)->toBe($group->id)
            ->and($invoice->company_id)->toBe($this->companyB->id)
            ->and($invoice->xmls()->count())->toBe(2);
    });

    test('empresas sem grupo geram faturas separadas', function () {
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179'), nfseFile(poolKey(2), '27270649000189')],
        ], ['Accept' => 'application/json'])->assertOk();

        expect(Invoice::count())->toBe(2);
    });

    test('fatura encerrada marca a nota com erro', function () {
        Invoice::create([
            'user_id' => $this->user->id, 'company_id' => $this->companyA->id,
            'title' => 'Março', 'reference_month' => '2026-03', 'status' => Invoice::STATUS_CLOSED,
        ]);

        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json'])->assertOk();

        $xml = InvoiceXml::first();
        expect($xml->status)->toBe('error')
            ->and($xml->invoice_id)->toBeNull()
            ->and($xml->parse_error)->toContain('encerrada');
    });

    test('tomador não cadastrado marca a nota com erro', function () {
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '99999999000199')],
        ], ['Accept' => 'application/json'])->assertOk();

        $xml = InvoiceXml::first();
        expect($xml->status)->toBe('error')->and($xml->parse_error)->toContain('não está cadastrado');
        expect(Invoice::count())->toBe(0);
    });

    test('nota duplicada marca erro e não soma no faturamento', function () {
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json']);
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json']);

        expect(InvoiceXml::where('status', 'error')->count())->toBe(1)
            ->and(InvoiceXml::where('xml_parsed', true)->count())->toBe(1);
    });

    test('reprocessa nota com erro após cadastrar o tomador', function () {
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '36821225000156')],
        ], ['Accept' => 'application/json']);
        $xml = InvoiceXml::first();
        expect($xml->status)->toBe('error');

        Company::create(['user_id' => $this->user->id, 'name' => 'Cliente C', 'cnpj' => '36.821.225/0001-56', 'active' => true]);

        $this->actingAs($this->user)->postJson("/invoice-pool/{$xml->uuid}/retry")->assertOk();

        expect($xml->fresh()->status)->toBe('processed');
    });
});

describe('DANFSe', function () {
    test('converte o XML nacional no payload da biblioteca', function () {
        $payload = app(DanfseService::class)->toPayload(nfseXml(poolKey(1), '06367953000179', '2026-04', 1200, 1100));

        $inf = $payload['infNfse'];
        expect($inf['numeroNfse'])->toBe('42')
            ->and($inf['emitente']['cnpj'])->toBe('11222333000181')
            ->and($inf['valores']['valorLiquido'])->toBe('1100')
            ->and($inf['dps']['infDps']['dataCompetencia'])->toBe('2026-04-01')
            ->and($inf['dps']['infDps']['tomador']['cnpj'])->toBe('06367953000179')
            ->and($inf['dps']['infDps']['servico']['codigoServico']['codigoTributacaoNacional'])->toBe('010701')
            ->and($inf['dps']['infDps']['valores']['valorServicoPrestado']['valorServico'])->toBe('1200')
            ->and($inf['dps']['infDps']['valores']['tributacao']['tipoRetencaoIssqn'])->toBe('Não Retido');
    });

    test('gera o PDF do DANFSe para nota processada', function () {
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json']);
        $xml = InvoiceXml::first();

        $response = $this->actingAs($this->user)->get("/invoice-pool/{$xml->uuid}/danfse");

        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        expect(str_starts_with($response->getContent(), '%PDF'))->toBeTrue();
    });

    test('não gera DANFSe para nota fora do padrão nacional', function () {
        $xml = InvoiceXml::create([
            'user_id' => $this->user->id, 'status' => 'processed', 'xml_parsed' => true,
            'access_key' => '11222333000181-42-ABC', 'filename' => 'abrasf.xml', 'path' => 'abrasf.xml',
        ]);
        Storage::put('abrasf.xml', '<CompNfse><Nfse><InfNfse><Numero>42</Numero></InfNfse></Nfse></CompNfse>');

        expect($xml->supportsDanfse())->toBeFalse();
        $this->actingAs($this->user)->get("/invoice-pool/{$xml->uuid}/danfse")->assertNotFound();
    });
});

describe('Faturamento anual', function () {
    test('soma o valor bruto por competência e compara ao limite', function () {
        Setting::forUser($this->user->id)->update(['annual_revenue_limit' => 10000]);

        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [
                nfseFile(poolKey(1), '06367953000179', '2026-01', 3000),
                nfseFile(poolKey(2), '06367953000179', '2026-02', 6000),
            ],
        ], ['Accept' => 'application/json']);

        $response = $this->actingAs($this->user)->get('/invoice-pool?year=2026');

        $response->assertOk();
        $summary = $response->viewData('summary');
        expect($summary['gross'])->toBe(9000.0)
            ->and($summary['percentage'])->toBe(90.0)
            ->and($summary['level'])->toBe('warning')
            ->and($summary['remaining'])->toBe(1000.0);
    });

    test('atualiza o limite anual', function () {
        $this->actingAs($this->user)->putJson('/invoice-pool/revenue-limit', ['annual_revenue_limit' => 81000])
            ->assertOk();

        expect((float) Setting::forUser($this->user->id)->annual_revenue_limit)->toBe(81000.0);
    });
});

describe('Isolamento', function () {
    test('usuário não acessa nota de outro usuário', function () {
        Queue::fake();
        $this->actingAs($this->user)->post('/invoice-pool', [
            'xmls' => [nfseFile(poolKey(1), '06367953000179')],
        ], ['Accept' => 'application/json']);
        $xml = InvoiceXml::first();

        $other = User::factory()->create();
        Subscription::create([
            'user_id' => $other->id, 'plan' => 'premium', 'status' => 'active',
            'starts_at' => now()->subDay(), 'ends_at' => now()->addYear(),
        ]);

        $this->actingAs($other)->get("/invoice-pool/{$xml->uuid}/download")->assertNotFound();
        $this->actingAs($other)->get("/invoice-pool/{$xml->uuid}/danfse")->assertNotFound();
        $this->actingAs($other)->deleteJson("/invoice-pool/{$xml->uuid}")->assertNotFound();
    });

    test('grupo não aceita empresa de outro usuário', function () {
        $other = User::factory()->create();
        $foreign = Company::create(['user_id' => $other->id, 'name' => 'Alheia', 'cnpj' => '11.111.111/0001-11', 'active' => true]);

        $this->actingAs($this->user)->postJson('/billing-groups', [
            'name' => 'Grupo', 'company_ids' => [$this->companyA->id, $foreign->id],
        ])->assertStatus(422);
    });
});
