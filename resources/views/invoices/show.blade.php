<x-app-layout>
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
@php
    $statusColors = [
        'rascunho'   => 'bg-gray-700 text-gray-300',
        'aberta'     => 'bg-blue-900/40 text-blue-300',
        'conciliada' => 'bg-emerald-900/40 text-emerald-300',
        'encerrada'  => 'bg-gray-800 text-gray-400',
        'cancelada'  => 'bg-red-900/40 text-red-400',
    ];
    $statusLabels = [
        'rascunho'   => 'Rascunho',
        'aberta'     => 'Aberta',
        'conciliada' => 'Conciliada',
        'encerrada'  => 'Encerrada',
        'cancelada'  => 'Cancelada',
    ];
    $statusNext = [
        'rascunho'   => ['value' => 'aberta',     'label' => 'Abrir Fatura'],
        'aberta'     => ['value' => 'conciliada', 'label' => 'Marcar Conciliada'],
        'conciliada' => ['value' => 'encerrada',  'label' => 'Encerrar Fatura'],
        'encerrada'  => null,
        'cancelada'  => null,
    ];
@endphp
<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="invoiceShow()">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 gap-4">
        <div class="flex items-start gap-3 min-w-0">
            <a href="{{ route('invoices.index') }}" class="mt-1 text-gray-400 hover:text-white transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-white" x-text="invoiceTitle">{{ $invoice->title }}</h1>
                    <span class="text-sm px-2.5 py-0.5 rounded-full {{ $statusColors[$invoice->status] }}">
                        {{ $statusLabels[$invoice->status] }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-400 flex-wrap">
                    <span>{{ \Carbon\Carbon::parse($invoice->reference_month . '-01')->translatedFormat('F Y') }}</span>
                    @if($invoice->company)
                        <span class="text-gray-600">·</span>
                        <span>{{ $invoice->company->name }}</span>
                    @endif
                    @if($invoice->bankAccount)
                        <span class="text-gray-600">·</span>
                        <span>{{ $invoice->bankAccount->bank_name }} – {{ $invoice->bankAccount->account_number }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @if(!$invoice->isClosed())
                <button @click="openEditInvoice()" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 border border-gray-700 px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </button>
            @endif
            <a href="{{ route('invoices.pdf', $invoice->uuid) }}" target="_blank"
               class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 border border-gray-700 px-3 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                PDF
            </a>
            <button @click="openEmailModal()"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 border border-gray-700 px-3 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Enviar
            </button>
            @if(!in_array($invoice->status, ['encerrada', 'cancelada']))
                <button @click="cancelInvoice()"
                        class="inline-flex items-center gap-1.5 text-sm text-red-400 hover:text-white bg-red-900/30 hover:bg-red-700 border border-red-800 px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancelar
                </button>
            @endif
            @if($statusNext[$invoice->status])
                <button @click="updateStatus('{{ $statusNext[$invoice->status]['value'] }}')"
                        class="inline-flex items-center gap-1.5 text-sm font-medium
                               {{ $statusNext[$invoice->status]['value'] === 'encerrada' ? 'bg-gray-700 hover:bg-gray-600 text-white' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}
                               px-3 py-2 rounded-lg transition-colors">
                    {{ $statusNext[$invoice->status]['label'] }}
                </button>
            @endif
        </div>
    </div>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-400 mb-1">Créditos</p>
            <p class="text-lg font-semibold text-emerald-400" x-text="'R$ ' + formatMoney(totals.total_credits)">
                R$ {{ number_format($invoice->getTotalCredits(), 2, ',', '.') }}
            </p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-400 mb-1">Débitos</p>
            <p class="text-lg font-semibold text-red-400" x-text="'R$ ' + formatMoney(totals.total_debits)">
                R$ {{ number_format($invoice->getTotalDebits(), 2, ',', '.') }}
            </p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-400 mb-1">Líquido</p>
            <p class="text-lg font-semibold" :class="totals.net_total >= 0 ? 'text-white' : 'text-red-400'"
               x-text="'R$ ' + formatMoney(totals.net_total)">
                R$ {{ number_format($invoice->getNetTotal(), 2, ',', '.') }}
            </p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-xs text-gray-400 mb-1">Conciliação</p>
            <p class="text-sm font-semibold mt-0.5"
               :class="{
                   'text-emerald-400': totals.reconciliation === 'conciliado',
                   'text-yellow-400':  totals.reconciliation === 'parcial',
                   'text-gray-400':    totals.reconciliation === 'pendente',
               }"
               x-text="{ conciliado: '✓ Conciliado', parcial: '⚠ Parcial', pendente: '— Pendente' }[totals.reconciliation]">
                @php $rec = $invoice->getReconciliationStatus(); @endphp
                {{ ['conciliado' => '✓ Conciliado', 'parcial' => '⚠ Parcial', 'pendente' => '— Pendente'][$rec] }}
            </p>
            <p class="text-xs text-gray-500 mt-0.5"
               x-text="'NFs: R$ ' + formatMoney(totals.xml_total)">
                NFs: R$ {{ number_format($invoice->getXmlTotal(), 2, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Lançamentos --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-800">
                <h2 class="font-semibold text-white">Lançamentos</h2>
                @if(!$invoice->isClosed())
                    <div class="flex items-center gap-2">
                        <button @click="openImportModal()"
                                class="inline-flex items-center gap-1 text-xs text-purple-400 hover:text-purple-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Importar
                        </button>
                        <button @click="openAddEntry()"
                                class="inline-flex items-center gap-1 text-xs text-emerald-400 hover:text-emerald-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar
                        </button>
                    </div>
                @endif
            </div>

            <div class="divide-y divide-gray-800" id="entries-list">
                <template x-if="entries.length === 0">
                    <div class="px-5 py-8 text-center text-gray-500 text-sm">Nenhum lançamento ainda.</div>
                </template>
                <template x-for="entry in entries" :key="entry.uuid">
                    <div class="flex items-start justify-between px-5 py-3 gap-3 hover:bg-gray-800/30 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-white truncate" x-text="entry.description"></p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                <span x-text="formatDateBR(entry.date)"></span>
                                <span x-show="entry.reconcile_with_xml" class="text-cyan-500 ml-1">· vinculado à NF</span>
                                <span x-show="entry.time_entry_id" class="text-purple-400 ml-1">· hora vinculada</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-sm font-medium"
                                  :class="entry.type === 'credit' ? 'text-emerald-400' : 'text-red-400'"
                                  x-text="(entry.type === 'credit' ? '+ ' : '- ') + 'R$ ' + formatMoney(entry.amount)">
                            </span>
                            @if(!$invoice->isClosed())
                            <div class="flex gap-1">
                                <button @click="openEditEntry(entry)"
                                        class="text-gray-500 hover:text-gray-300 transition-colors p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteEntry(entry.uuid)"
                                        class="text-gray-500 hover:text-red-400 transition-colors p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- XMLs / Notas Fiscais --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-800">
                <h2 class="font-semibold text-white">Notas Fiscais (XML)</h2>
                @if(!$invoice->isClosed())
                    <label class="inline-flex items-center gap-1 text-xs text-cyan-400 hover:text-cyan-300 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Importar XML
                        <input type="file" accept=".xml" multiple class="hidden" @change="uploadXml($event)">
                    </label>
                @endif
            </div>

            <div class="divide-y divide-gray-800" id="xmls-list">
                @forelse($invoice->xmls as $xml)
                <div class="px-5 py-3 hover:bg-gray-800/30 transition-colors" data-xml-uuid="{{ $xml->uuid }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm text-white truncate">{{ $xml->filename }}</p>
                                @if($xml->xml_parsed)
                                    <span class="text-xs text-emerald-400">✓</span>
                                @else
                                    <span class="text-xs text-yellow-400" title="{{ $xml->parse_error }}">⚠</span>
                                @endif
                            </div>
                            @if($xml->xml_parsed)
                            <div class="text-xs text-gray-400 mt-0.5 space-y-0.5">
                                @if($xml->invoice_number)
                                    <p>NF {{ $xml->invoice_number }} @if($xml->issued_at)· {{ $xml->issued_at->format('d/m/Y') }}@endif</p>
                                @endif
                                @if($xml->provider_name)
                                    <p class="truncate">{{ $xml->provider_name }}</p>
                                @endif
                            </div>
                            @else
                            <p class="text-xs text-yellow-400/80 mt-0.5">{{ $xml->parse_error ?? 'Não processado' }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($xml->amount)
                                <span class="text-sm font-medium text-white">R$ {{ number_format($xml->amount, 2, ',', '.') }}</span>
                            @endif
                            {{-- DANFSe --}}
                            @if($xml->danfse_path)
                                <a href="{{ route('invoices.xmls.danfse.view', [$invoice->uuid, $xml->uuid]) }}"
                                   target="_blank"
                                   title="Visualizar DANFSe"
                                   class="text-cyan-500 hover:text-cyan-300 transition-colors p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                                @if(!$invoice->isClosed())
                                <button onclick="deleteDanfse('{{ $invoice->uuid }}', '{{ $xml->uuid }}')"
                                        title="Remover DANFSe"
                                        class="text-gray-500 hover:text-red-400 transition-colors p-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                @endif
                            @elseif(!$invoice->isClosed())
                                <label title="Importar DANFSe (PDF)" class="cursor-pointer text-gray-500 hover:text-cyan-400 transition-colors p-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <input type="file" accept=".pdf" class="hidden" onchange="uploadDanfse('{{ $invoice->uuid }}', '{{ $xml->uuid }}', this)">
                                </label>
                            @endif
                            <a href="{{ route('invoices.xmls.download', [$invoice->uuid, $xml->uuid]) }}"
                               title="Baixar XML"
                               class="text-gray-500 hover:text-gray-300 transition-colors p-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                            @if(!$invoice->isClosed())
                            <button @click="deleteXml('{{ $xml->uuid }}')"
                                    class="text-gray-500 hover:text-red-400 transition-colors p-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-500 text-sm">
                    Nenhum XML importado ainda.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Observações --}}
    @if($invoice->notes)
    <div class="mt-4 bg-gray-900 border border-gray-800 rounded-xl p-5">
        <h3 class="text-sm font-medium text-gray-400 mb-1">Observações</h3>
        <p class="text-sm text-gray-300">{{ $invoice->notes }}</p>
    </div>
    @endif

    {{-- Auditoria --}}
    <div class="mt-4 bg-gray-900 border border-gray-800 rounded-xl">
        <details>
            <summary class="px-5 py-4 cursor-pointer flex items-center justify-between select-none">
                <h3 class="text-sm font-medium text-gray-400">Histórico da Fatura</h3>
                <span class="text-xs text-gray-600">{{ $invoice->auditLogs->count() }} evento(s)</span>
            </summary>
            <div class="divide-y divide-gray-800/60 px-5 pb-4">
                @forelse($invoice->auditLogs as $log)
                <div class="flex items-start gap-3 py-2.5">
                    <div class="mt-1 w-1.5 h-1.5 rounded-full shrink-0 {{ str_replace('text-', 'bg-', $log->actionColor()) }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-200">{{ $log->description }}</p>
                        @if($log->metadata && isset($log->metadata['de'], $log->metadata['para']))
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ ucfirst($log->metadata['de']) }} → {{ ucfirst($log->metadata['para']) }}
                            </p>
                        @elseif($log->metadata && isset($log->metadata['destinatario']))
                            <p class="text-xs text-gray-500 mt-0.5">{{ $log->metadata['destinatario'] }}</p>
                        @endif
                    </div>
                    <time class="text-xs text-gray-500 shrink-0 whitespace-nowrap">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </time>
                </div>
                @empty
                <p class="text-sm text-gray-500 py-4">Nenhum evento registrado ainda. Ações futuras (mudanças de status, XMLs, e-mails) aparecerão aqui.</p>
                @endforelse
            </div>
        </details>
    </div>

    {{-- Modal E-mail --}}
    <div x-show="showEmailModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
         @click.self="showEmailModal = false"
         style="display:none">
        <div class="bg-gray-900 border border-gray-700 rounded-xl w-full max-w-md shadow-2xl" @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <h2 class="text-lg font-semibold text-white">Enviar Fatura por E-mail</h2>
                <button @click="showEmailModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form @submit.prevent="submitEmail()" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">E-mail do destinatário <span class="text-red-400">*</span></label>
                    <input type="email" x-model="emailForm.recipient_email"
                           placeholder="exemplo@empresa.com.br"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-500">
                    <p x-show="emailErrors.recipient_email" x-text="emailErrors.recipient_email" class="text-red-400 text-xs mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Mensagem <span class="text-gray-500">(opcional)</span></label>
                    <textarea x-model="emailForm.message" rows="3"
                              placeholder="Mensagem adicional para o destinatário..."
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-500 resize-none"></textarea>
                </div>
                <p class="text-xs text-gray-500">O PDF da fatura será anexado automaticamente ao e-mail.</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEmailModal = false"
                            class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="emailLoading"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                        <span x-show="!emailLoading">Enviar</span>
                        <span x-show="emailLoading">Enviando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Lançamento --}}
    <div x-show="showEntryModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
         @click.self="showEntryModal = false"
         style="display:none">
        <div class="bg-gray-900 border border-gray-700 rounded-xl w-full max-w-md shadow-2xl" @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <h2 class="text-lg font-semibold text-white" x-text="editingEntry ? 'Editar Lançamento' : 'Novo Lançamento'"></h2>
                <button @click="showEntryModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form @submit.prevent="submitEntry()" class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Tipo <span class="text-red-400">*</span></label>
                        <select x-model="entryForm.type"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                            <option value="credit">Crédito</option>
                            <option value="debit">Débito</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Valor (R$) <span class="text-red-400">*</span></label>
                        <input type="text" inputmode="numeric" :value="entryAmountDisplay"
                               @input="onAmountInput($event)"
                               placeholder="0,00"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                        <p x-show="entryErrors.amount" x-text="entryErrors.amount" class="text-red-400 text-xs mt-1"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Descrição <span class="text-red-400">*</span></label>
                    <input type="text" x-model="entryForm.description" placeholder="Ex: Serviço de desenvolvimento"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <p x-show="entryErrors.description" x-text="entryErrors.description" class="text-red-400 text-xs mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Data <span class="text-red-400">*</span></label>
                    <input type="date" x-model="entryForm.date"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <p x-show="entryErrors.date" x-text="entryErrors.date" class="text-red-400 text-xs mt-1"></p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="reconcile_with_xml" x-model="entryForm.reconcile_with_xml"
                           class="rounded border-gray-600 bg-gray-800 text-emerald-500">
                    <label for="reconcile_with_xml" class="text-sm text-gray-300">Considerar na conciliação com NF (XML)</label>
                </div>
                <div x-show="availableTimeEntries.length > 0">
                    <label class="block text-sm text-gray-300 mb-1">Vincular a lançamento de horas <span class="text-gray-500">(opcional)</span></label>
                    <select x-model="entryForm.time_entry_id"
                            @change="onTimeEntrySelect()"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                        <option value="">— Nenhum —</option>
                        <template x-for="te in availableTimeEntries" :key="te.id">
                            <option :value="te.id"
                                    :disabled="te.invoice_id && te.invoice_id != currentInvoiceId"
                                    x-text="te.date_br + ' · ' + te.project_name + ' · ' + formatHours(te.hours) + 'h' + (te.description ? ' — ' + te.description.substring(0,40) : '')">
                            </option>
                        </template>
                    </select>
                    <p x-show="entryForm.time_entry_id && availableTimeEntries.find(t => t.id == entryForm.time_entry_id)?.invoice_id"
                       class="text-yellow-400 text-xs mt-1">Este lançamento já está vinculado a esta fatura.</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEntryModal = false"
                            class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="entryLoading"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                        <span x-show="!entryLoading" x-text="editingEntry ? 'Salvar' : 'Adicionar'"></span>
                        <span x-show="entryLoading">Salvando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Importar Lançamentos de Horas --}}
    <div x-show="showImportModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
         @click.self="showImportModal = false"
         style="display:none">
        <div class="bg-gray-900 border border-gray-700 rounded-xl w-full max-w-lg shadow-2xl" @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <h2 class="text-lg font-semibold text-white">Importar Lançamentos de Horas</h2>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <div x-show="importLoading" class="py-8 text-center text-gray-400 text-sm">Carregando lançamentos...</div>
                <div x-show="!importLoading && importableEntries.length === 0" class="py-8 text-center text-gray-500 text-sm">
                    Nenhum lançamento disponível para importação neste mês.
                </div>
                <div x-show="!importLoading && importableEntries.length > 0" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Valor/hora (R$) <span class="text-red-400">*</span></label>
                            <input type="text" inputmode="numeric"
                                   :value="importRateDisplay"
                                   @input="onImportRateInput($event)"
                                   placeholder="0,00"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-500">
                        </div>
                        <div class="flex items-end">
                            <p class="text-xs text-gray-500 pb-2">O valor de cada lançamento será calculado automaticamente (horas × valor/hora).</p>
                        </div>
                    </div>

                    <div class="max-h-64 overflow-y-auto divide-y divide-gray-800 border border-gray-800 rounded-lg">
                        <template x-for="te in importableEntries" :key="te.id">
                            <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-800/40 cursor-pointer">
                                <input type="checkbox" :value="te.id" x-model="selectedImportIds"
                                       class="rounded border-gray-600 bg-gray-800 text-purple-500 shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-white truncate" x-text="te.description || te.project_name"></p>
                                    <p class="text-xs text-gray-400">
                                        <span x-text="te.date_br"></span>
                                        · <span x-text="te.project_name"></span>
                                        · <span x-text="formatHours(te.hours) + 'h'"></span>
                                    </p>
                                </div>
                                <span class="text-sm font-medium text-purple-300 shrink-0"
                                      x-text="importRate > 0 ? 'R$ ' + formatMoney(te.hours * importRate) : '—'">
                                </span>
                            </label>
                        </template>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <p class="text-xs text-gray-500">
                            <span x-text="selectedImportIds.length"></span> selecionado(s)
                        </p>
                        <div class="flex gap-3">
                            <button type="button" @click="showImportModal = false"
                                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                                Cancelar
                            </button>
                            <button type="button" @click="submitImport()"
                                    :disabled="!selectedImportIds.length || importRate <= 0 || importSubmitting"
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                                <span x-show="!importSubmitting">Importar</span>
                                <span x-show="importSubmitting">Importando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Editar Fatura --}}
    <div x-show="showEditModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
         @click.self="showEditModal = false"
         style="display:none">
        <div class="bg-gray-900 border border-gray-700 rounded-xl w-full max-w-lg shadow-2xl" @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <h2 class="text-lg font-semibold text-white">Editar Fatura</h2>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form @submit.prevent="submitEditInvoice()" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Título <span class="text-red-400">*</span></label>
                    <input type="text" x-model="editForm.title"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Competência <span class="text-red-400">*</span></label>
                    <input type="month" x-model="editForm.reference_month"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Empresa</label>
                        <select x-model="editForm.company_id"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                            <option value="">Nenhuma</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Conta Bancária</label>
                        <select x-model="editForm.bank_account_id"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                            <option value="">Nenhuma</option>
                            @foreach($bankAccounts as $ba)
                                <option value="{{ $ba->id }}">{{ $ba->bank_name }} – {{ $ba->account_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Observações</label>
                    <textarea x-model="editForm.notes" rows="2"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="editLoading"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                        <span x-show="!editLoading">Salvar</span>
                        <span x-show="editLoading">Salvando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function invoiceShow() {
    return {
        invoiceUuid: '{{ $invoice->uuid }}',
        invoiceTitle: '{{ addslashes($invoice->title) }}',
        currentInvoiceId: {{ $invoice->id }},
        totals: {
            total_credits:      {{ $invoice->getTotalCredits() }},
            total_debits:       {{ $invoice->getTotalDebits() }},
            net_total:          {{ $invoice->getNetTotal() }},
            reconcilable_total: {{ $invoice->getReconcilableTotal() }},
            xml_total:          {{ $invoice->getXmlTotal() }},
            reconciliation:     '{{ $invoice->getReconciliationStatus() }}',
        },

        // Lista de lançamentos (Alpine state — sem reload)
        entries: {!! $invoice->entries->map(fn($e) => [
            'uuid'             => $e->uuid,
            'type'             => $e->type,
            'description'      => $e->description,
            'amount'           => (float) $e->amount,
            'date'             => $e->date->format('Y-m-d'),
            'reconcile_with_xml' => (bool) $e->reconcile_with_xml,
            'time_entry_id'    => $e->time_entry_id,
            'sort_order'       => $e->sort_order,
        ])->values()->toJson() !!},

        // Entry modal
        showEntryModal:    false,
        editingEntry:      null,
        entryLoading:      false,
        entryErrors:       {},
        entryAmountDisplay: '',
        entryForm: { type: 'credit', description: '', amount: '', date: '', reconcile_with_xml: false, time_entry_id: '' },

        // Time entries disponíveis para vincular
        availableTimeEntries: [],

        // Import modal
        showImportModal:    false,
        importLoading:      false,
        importSubmitting:   false,
        importableEntries:  [],
        selectedImportIds:  [],
        importRate:         0,
        importRateDisplay:  '',

        // Email modal
        showEmailModal: false,
        emailLoading:   false,
        emailErrors:    {},
        emailForm: {
            recipient_email: '{{ addslashes($invoice->company?->email ?? "") }}',
            message: '',
        },

        // Edit invoice modal
        showEditModal: false,
        editLoading:   false,
        editForm: {
            title:           '{{ addslashes($invoice->title) }}',
            reference_month: '{{ $invoice->reference_month }}',
            company_id:      '{{ $invoice->company_id ?? "" }}',
            bank_account_id: '{{ $invoice->bank_account_id ?? "" }}',
            notes:           '{{ addslashes($invoice->notes ?? "") }}',
        },

        // ==================== HELPERS ====================

        formatMoney(value) {
            return Math.abs(parseFloat(value) || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        formatHours(hours) {
            const h = Math.floor(hours);
            const m = Math.round((hours - h) * 60);
            return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        },

        formatDateBR(dateStr) {
            if (!dateStr) return '';
            // Normalize: remove time portion (ex: "2026-03-15T00:00:00.000000Z" → "2026-03-15")
            const datePart = dateStr.split('T')[0];
            const parts = datePart.split('-');
            if (parts.length < 3) return dateStr;
            const [y, m, d] = parts;
            return `${d}/${m}/${y}`;
        },

        parseBRLMask(value) {
            const raw = String(value).replace(/\D/g, '');
            if (!raw) return 0;
            return parseInt(raw, 10) / 100;
        },

        applyBRLMask(numericValue) {
            if (!numericValue && numericValue !== 0) return '';
            return parseFloat(numericValue).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // ==================== ENTRY FORM ====================

        async openAddEntry() {
            this.editingEntry = null;
            this.entryForm = {
                type: 'credit',
                description: '',
                amount: '',
                date: new Date().toISOString().slice(0, 10),
                reconcile_with_xml: false,
                time_entry_id: '',
            };
            this.entryAmountDisplay = '';
            this.entryErrors = {};
            await this.loadAvailableTimeEntries();
            this.showEntryModal = true;
        },

        async openEditEntry(entry) {
            this.editingEntry = entry;
            this.entryForm = {
                type:             entry.type,
                description:      entry.description,
                amount:           entry.amount,
                date:             entry.date,
                reconcile_with_xml: entry.reconcile_with_xml,
                time_entry_id:    entry.time_entry_id || '',
            };
            this.entryAmountDisplay = this.applyBRLMask(entry.amount);
            this.entryErrors = {};
            await this.loadAvailableTimeEntries();
            this.showEntryModal = true;
        },

        onAmountInput(event) {
            const raw = event.target.value.replace(/\D/g, '');
            if (!raw) {
                this.entryAmountDisplay = '';
                this.entryForm.amount = '';
                return;
            }
            const numericValue = parseInt(raw, 10) / 100;
            this.entryForm.amount = numericValue;
            this.entryAmountDisplay = numericValue.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        onTimeEntrySelect() {
            if (!this.entryForm.time_entry_id) return;
            const te = this.availableTimeEntries.find(t => t.id == this.entryForm.time_entry_id);
            if (!te) return;
            if (!this.entryForm.description && te.description) {
                this.entryForm.description = te.description;
            }
            if (!this.entryForm.description && te.project_name) {
                this.entryForm.description = te.project_name;
            }
            this.entryForm.date = te.date;
        },

        async loadAvailableTimeEntries() {
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/available-time-entries`, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    this.availableTimeEntries = data.entries;
                }
            } catch (e) { /* silently ignore */ }
        },

        async submitEntry() {
            this.entryLoading = true;
            this.entryErrors = {};
            const url = this.editingEntry
                ? `/invoices/${this.invoiceUuid}/entries/${this.editingEntry.uuid}`
                : `/invoices/${this.invoiceUuid}/entries`;
            const method = this.editingEntry ? 'PUT' : 'POST';

            const payload = { ...this.entryForm };
            if (!payload.time_entry_id) delete payload.time_entry_id;

            try {
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) {
                    const entry = {
                        uuid:              data.entry.uuid,
                        type:              data.entry.type,
                        description:       data.entry.description,
                        amount:            parseFloat(data.entry.amount),
                        date:              (data.entry.date || '').split('T')[0],
                        reconcile_with_xml: data.entry.reconcile_with_xml,
                        time_entry_id:     data.entry.time_entry_id || null,
                        sort_order:        data.entry.sort_order,
                    };
                    if (this.editingEntry) {
                        const idx = this.entries.findIndex(e => e.uuid === entry.uuid);
                        if (idx !== -1) this.entries[idx] = entry;
                        else this.entries.push(entry);
                    } else {
                        this.entries.push(entry);
                    }
                    this.totals = data.totals;
                    this.showEntryModal = false;
                    // Atualiza disponíveis (não bloqueia UX)
                    this.loadAvailableTimeEntries();
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                } else if (data.errors) {
                    this.entryErrors = data.errors;
                } else {
                    showToast(data.message || 'Erro ao salvar lançamento.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao salvar lançamento.', TOAST_TYPES.ERROR);
            } finally {
                this.entryLoading = false;
            }
        },

        deleteEntry(uuid) {
            showConfirm('Remover este lançamento?', () => this._doDeleteEntry(uuid), 'Remover Lançamento', 'Remover');
        },

        async _doDeleteEntry(uuid) {
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/entries/${uuid}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    this.entries = this.entries.filter(e => e.uuid !== uuid);
                    this.totals = data.totals;
                    this.loadAvailableTimeEntries();
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                } else {
                    showToast(data.message, TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao remover lançamento.', TOAST_TYPES.ERROR);
            }
        },

        // ==================== IMPORT MODAL ====================

        async openImportModal() {
            this.showImportModal = true;
            this.selectedImportIds = [];
            this.importRate = 0;
            this.importRateDisplay = '';
            this.importLoading = true;
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/available-time-entries`, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    // Apenas não vinculados (ou vinculados a esta fatura)
                    this.importableEntries = data.entries.filter(e => !e.invoice_id || e.invoice_id === this.currentInvoiceId);
                    // Remove os que já viraram lançamento nesta fatura
                    const linkedIds = this.entries.filter(e => e.time_entry_id).map(e => e.time_entry_id);
                    this.importableEntries = this.importableEntries.filter(e => !linkedIds.includes(e.id));
                }
            } catch (e) {
                showToast('Erro ao carregar lançamentos.', TOAST_TYPES.ERROR);
            } finally {
                this.importLoading = false;
            }
        },

        onImportRateInput(event) {
            const raw = event.target.value.replace(/\D/g, '');
            if (!raw) { this.importRateDisplay = ''; this.importRate = 0; return; }
            this.importRate = parseInt(raw, 10) / 100;
            this.importRateDisplay = this.importRate.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        async submitImport() {
            if (!this.selectedImportIds.length || this.importRate <= 0) return;
            this.importSubmitting = true;
            let imported = 0;
            let lastTotals = null;

            for (const id of this.selectedImportIds) {
                const te = this.importableEntries.find(e => e.id === id);
                if (!te) continue;
                const amount = parseFloat((this.importRate * te.hours).toFixed(2));
                if (amount <= 0) continue;
                try {
                    const res = await fetch(`/invoices/${this.invoiceUuid}/entries`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                        body: JSON.stringify({
                            type:             'credit',
                            description:      te.description || te.project_name,
                            amount,
                            date:             te.date,
                            time_entry_id:    te.id,
                            reconcile_with_xml: false,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.entries.push({
                            uuid:              data.entry.uuid,
                            type:              data.entry.type,
                            description:       data.entry.description,
                            amount:            parseFloat(data.entry.amount),
                            date:              (data.entry.date || '').split('T')[0],
                            reconcile_with_xml: data.entry.reconcile_with_xml,
                            time_entry_id:     data.entry.time_entry_id || null,
                            sort_order:        data.entry.sort_order,
                        });
                        lastTotals = data.totals;
                        imported++;
                    }
                } catch (e) { /* continue */ }
            }

            if (lastTotals) this.totals = lastTotals;
            this.importSubmitting = false;
            this.showImportModal = false;
            if (imported > 0) {
                showToast(`${imported} lançamento(s) importado(s) com sucesso.`, TOAST_TYPES.SUCCESS);
            } else {
                showToast('Nenhum lançamento importado.', TOAST_TYPES.WARNING);
            }
        },

        // ==================== XML ====================

        async uploadXml(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;
            event.target.value = '';

            const formData = new FormData();
            files.forEach(f => formData.append('xmls[]', f));

            const label = files.length === 1 ? 'XML' : `${files.length} XMLs`;
            showToast(`Importando ${label}...`, TOAST_TYPES.INFO);

            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/xmls`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    if (data.reconciliation === 'conciliado') {
                        showConfirm(
                            'Os valores da NF e da fatura estão conciliados. Deseja encerrar a fatura agora?',
                            () => this._doUpdateStatus('encerrada'),
                            'Conciliação OK',
                            'Encerrar Fatura'
                        );
                    } else {
                        window.location.reload();
                    }
                } else {
                    showToast(data.message || 'Erro ao importar XML.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao importar XML.', TOAST_TYPES.ERROR);
            }
        },

        deleteXml(uuid) {
            showConfirm('Remover este XML da fatura?', () => this._doDeleteXml(uuid), 'Remover XML', 'Remover');
        },

        async _doDeleteXml(uuid) {
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/xmls/${uuid}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    this.totals.xml_total = data.xml_total;
                    this.totals.reconciliation = data.reconciliation;
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    window.location.reload();
                } else {
                    showToast(data.message, TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao remover XML.', TOAST_TYPES.ERROR);
            }
        },

        // ==================== STATUS ====================

        updateStatus(newStatus) {
            if (newStatus === 'encerrada') {
                showConfirm('Deseja encerrar esta fatura? Esta ação não poderá ser desfeita.', () => this._doUpdateStatus(newStatus), 'Encerrar Fatura', 'Encerrar');
            } else {
                this._doUpdateStatus(newStatus);
            }
        },

        async _doUpdateStatus(newStatus) {
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ status: newStatus }),
                });
                const data = await res.json();
                if (data.success) {
                    if (data.warning) showToast(data.warning, TOAST_TYPES.WARNING);
                    else showToast(data.message, TOAST_TYPES.SUCCESS);
                    window.location.reload();
                } else {
                    showToast(data.message, TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao atualizar status.', TOAST_TYPES.ERROR);
            }
        },

        // ==================== CANCEL INVOICE ====================

        cancelInvoice() {
            showConfirm(
                'Cancelar esta fatura irá liberar todos os lançamentos vinculados. Deseja continuar?',
                () => this._doCancelInvoice(),
                'Cancelar Fatura',
                'Cancelar Fatura'
            );
        },

        async _doCancelInvoice() {
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/cancel`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    window.location.reload();
                } else {
                    showToast(data.message, TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao cancelar fatura.', TOAST_TYPES.ERROR);
            }
        },

        // ==================== EMAIL ====================

        openEmailModal() {
            this.emailErrors = {};
            this.showEmailModal = true;
        },

        async submitEmail() {
            this.emailLoading = true;
            this.emailErrors = {};
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}/send-email`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify(this.emailForm),
                });
                const data = await res.json();
                if (data.success) {
                    this.showEmailModal = false;
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                } else if (data.errors) {
                    this.emailErrors = data.errors;
                } else {
                    showToast(data.message || 'Erro ao enviar e-mail.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao enviar e-mail.', TOAST_TYPES.ERROR);
            } finally {
                this.emailLoading = false;
            }
        },

        // ==================== EDIT INVOICE ====================

        openEditInvoice() {
            this.showEditModal = true;
        },

        async submitEditInvoice() {
            this.editLoading = true;
            try {
                const res = await fetch(`/invoices/${this.invoiceUuid}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify(this.editForm),
                });
                const data = await res.json();
                if (data.success) {
                    this.showEditModal = false;
                    this.invoiceTitle = data.invoice.title;
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    window.location.reload();
                } else {
                    showToast(data.message || 'Erro ao salvar.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao salvar.', TOAST_TYPES.ERROR);
            } finally {
                this.editLoading = false;
            }
        },
    };
}

// DANFSe — funções globais (chamadas por onclick no Blade)
async function uploadDanfse(invoiceUuid, xmlUuid, input) {
    const file = input.files[0];
    if (!file) return;
    input.value = '';

    const formData = new FormData();
    formData.append('danfse', file);

    showToast('Importando DANFSe...', TOAST_TYPES.INFO);
    try {
        const res = await fetch(`/invoices/${invoiceUuid}/xmls/${xmlUuid}/danfse`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: formData,
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, TOAST_TYPES.SUCCESS);
            window.location.reload();
        } else {
            showToast(data.message || 'Erro ao importar DANFSe.', TOAST_TYPES.ERROR);
        }
    } catch (e) {
        showToast('Erro ao importar DANFSe.', TOAST_TYPES.ERROR);
    }
}

function deleteDanfse(invoiceUuid, xmlUuid) {
    showConfirm('Remover o DANFSe desta nota?', async () => {
        try {
            const res = await fetch(`/invoices/${invoiceUuid}/xmls/${xmlUuid}/danfse`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message, TOAST_TYPES.SUCCESS);
                window.location.reload();
            } else {
                showToast(data.message, TOAST_TYPES.ERROR);
            }
        } catch (e) {
            showToast('Erro ao remover DANFSe.', TOAST_TYPES.ERROR);
        }
    }, 'Remover DANFSe', 'Remover');
}
</script>
</x-app-layout>
