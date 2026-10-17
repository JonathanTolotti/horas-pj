<?php

namespace App\Jobs;

use App\Models\InvoiceXml;
use App\Services\InvoicePoolService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessInvoiceXml implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60];

    /**
     * Se a nota for excluída antes do processamento, o job é descartado.
     */
    public bool $deleteWhenMissingModels = true;

    public function __construct(public InvoiceXml $invoiceXml)
    {
        $this->afterCommit();
    }

    public function handle(InvoicePoolService $pool): void
    {
        $pool->process($this->invoiceXml->fresh());
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Falha ao processar nota do pool', [
            'invoice_xml_id' => $this->invoiceXml->id,
            'error'          => $exception?->getMessage(),
        ]);

        $this->invoiceXml->update([
            'status'      => InvoiceXml::STATUS_ERROR,
            'parse_error' => 'Erro inesperado ao processar a nota. Tente reprocessar.',
        ]);
    }
}
