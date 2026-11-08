<x-app-layout>
@php
    $brl = fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
    $monthNames = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                   '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
    $competenceLabel = function ($c) use ($monthNames) {
        [$y, $m] = explode('-', $c);
        return ($monthNames[$m] ?? $m) . ' de ' . $y;
    };
    $maxMonth = max(1, max(array_column($summary['months'], 'gross')), $summary['limit'] ? $summary['limit'] / 12 : 0);
    $levelStyles = [
        'ok'      => ['bar' => 'bg-emerald-500', 'badge' => 'bg-emerald-900/30 text-emerald-400', 'label' => 'Dentro do limite'],
        'warning' => ['bar' => 'bg-yellow-500',  'badge' => 'bg-yellow-900/30 text-yellow-400',   'label' => 'Atenção: acima de 80% do limite'],
        'over'    => ['bar' => 'bg-red-500',     'badge' => 'bg-red-900/40 text-red-400',         'label' => 'Limite ultrapassado'],
    ];
    $level = $levelStyles[$summary['level']] ?? null;
    $queuedPayload = $queued->map(fn ($x) => [
        'uuid' => $x->uuid, 'filename' => $x->filename, 'status' => $x->status, 'status_label' => $x->statusLabel(),
    ])->values();
@endphp

<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="invoicePoolPage()"
     @dragover.prevent @drop.prevent="onDrop($event)">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Notas Fiscais</h1>
            <p class="text-gray-400 text-sm mt-0.5">Envie suas notas, acompanhe o faturamento do ano e o vínculo com as faturas</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ route('invoice-pool.index') }}" class="flex items-center gap-2">
                <label for="pool-year" class="text-sm text-gray-400">Ano</label>
                <select id="pool-year" name="year" onchange="this.form.submit()"
                        class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" @click="$refs.poolFiles.click()" :disabled="uploading"
                    title="Até 50 XMLs por vez, 2 MB cada. Armazenamento: {{ $storageQuota['used_mb'] }} MB de {{ $storageQuota['quota_mb'] }} MB"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-60 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg x-show="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <svg x-show="uploading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                <span x-text="uploading ? 'Enviando...' : 'Enviar notas'">Enviar notas</span>
            </button>
            <input x-ref="poolFiles" type="file" multiple accept=".xml,text/xml,application/xml" class="sr-only" tabindex="-1" aria-label="Selecionar XMLs"
                   @change="upload($event.target.files); $event.target.value = ''">
        </div>
    </div>

    {{-- Faturamento anual x limite --}}
    <section class="bg-gray-900 border border-gray-800 rounded-xl p-5" aria-labelledby="annual-title">
        <div class="flex items-start justify-between flex-wrap gap-3 mb-4">
            <div>
                <h2 id="annual-title" class="text-lg font-semibold text-white">Faturamento de {{ $year }}</h2>
                <p class="text-xs text-gray-400">Valor bruto das notas por competência ({{ $summary['notes'] }} {{ $summary['notes'] === 1 ? 'nota' : 'notas' }})</p>
            </div>
            @if($level)
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $level['badge'] }}">
                    @if($summary['level'] === 'ok')
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    @endif
                    {{ $level['label'] }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
            <div>
                <p class="text-xs text-gray-400">Faturado (bruto)</p>
                <p class="text-xl font-bold text-white">{{ $brl($summary['gross']) }}</p>
                <p class="text-xs text-gray-500">Líquido: {{ $brl($summary['net']) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Limite anual</p>
                <template x-if="!editingLimit">
                    <div class="flex items-center gap-2">
                        <p class="text-xl font-bold text-white">{{ $summary['limit'] ? $brl($summary['limit']) : 'Não definido' }}</p>
                        <button type="button" @click="editingLimit = true" class="p-1 text-gray-400 hover:text-white rounded" title="Editar limite" aria-label="Editar limite anual">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                    </div>
                </template>
                <template x-if="editingLimit">
                    <div class="space-y-2 mt-1">
                        <div class="flex gap-2">
                            <input type="number" min="0" step="0.01" x-model="limit" aria-label="Limite anual em reais"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-white focus:outline-none focus:border-emerald-500">
                            <button type="button" @click="saveLimit()" class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm px-3 rounded-lg">Salvar</button>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <button type="button" @click="limit = 81000" class="text-xs bg-gray-800 hover:bg-gray-700 text-gray-300 px-2 py-0.5 rounded">MEI</button>
                            <button type="button" @click="limit = 360000" class="text-xs bg-gray-800 hover:bg-gray-700 text-gray-300 px-2 py-0.5 rounded">ME</button>
                            <button type="button" @click="limit = 4800000" class="text-xs bg-gray-800 hover:bg-gray-700 text-gray-300 px-2 py-0.5 rounded">Simples</button>
                            <button type="button" @click="editingLimit = false" class="text-xs text-gray-400 hover:text-white px-2 py-0.5">Cancelar</button>
                        </div>
                    </div>
                </template>
            </div>
            <div>
                <p class="text-xs text-gray-400">Disponível no ano</p>
                <p class="text-xl font-bold {{ ($summary['remaining'] ?? 0) < 0 ? 'text-red-400' : 'text-white' }}">
                    {{ $summary['remaining'] !== null ? $brl($summary['remaining']) : '—' }}
                </p>
                @if($summary['percentage'] !== null)
                    <p class="text-xs text-gray-500">{{ number_format($summary['percentage'], 1, ',', '.') }}% utilizado</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-400">Projeção para o ano</p>
                <p class="text-xl font-bold text-white">{{ $brl($summary['projection']) }}</p>
                @if($summary['projection_percentage'] !== null)
                    <p class="text-xs {{ $summary['projection_percentage'] >= 100 ? 'text-red-400' : 'text-gray-500' }}">
                        {{ number_format($summary['projection_percentage'], 1, ',', '.') }}% do limite (média mensal × 12)
                    </p>
                @endif
            </div>
        </div>

        @if($summary['percentage'] !== null)
            <div class="mb-6">
                <div class="h-2.5 bg-gray-800 rounded-full overflow-hidden" role="progressbar"
                     aria-valuenow="{{ min(100, $summary['percentage']) }}" aria-valuemin="0" aria-valuemax="100"
                     aria-label="Percentual do limite anual utilizado">
                    <div class="h-full rounded-full {{ $level['bar'] }}" style="width: {{ min(100, $summary['percentage']) }}%"></div>
                </div>
            </div>
        @endif

        {{-- Barras mensais (série única: sem legenda; linha tracejada = limite / 12) --}}
        <div class="relative" aria-hidden="true">
            @if($summary['limit'])
                <div class="absolute left-0 right-0 border-t border-dashed border-gray-500 pointer-events-none z-10"
                     style="bottom: calc(1.5rem + {{ ($summary['limit'] / 12) / $maxMonth * 100 }} * (10rem / 100))">
                    <span class="absolute -top-4 right-0 text-[10px] text-gray-400">média permitida {{ $brl($summary['limit'] / 12) }}/mês</span>
                </div>
            @endif
            <div class="grid grid-cols-12 gap-0.5 items-end">
                @foreach($summary['months'] as $m)
                    <div class="group relative flex flex-col items-center">
                        <div class="w-full h-40 flex items-end justify-center px-0.5 sm:px-1.5">
                            <div class="w-full rounded-t {{ $m['gross'] > 0 ? 'bg-emerald-500 group-hover:bg-emerald-400' : '' }} transition-colors"
                                 style="height: {{ $m['gross'] > 0 ? max(2, $m['gross'] / $maxMonth * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-[10px] sm:text-xs text-gray-400 h-6 leading-6">{{ $m['label'] }}</span>
                        <div class="hidden group-hover:block absolute bottom-full mb-1 z-20 whitespace-nowrap bg-gray-800 border border-gray-700 rounded-lg px-2.5 py-1.5 text-xs shadow-lg">
                            <p class="text-white font-medium">{{ $competenceLabel($m['competence']) }}</p>
                            <p class="text-gray-300">Bruto: {{ $brl($m['gross']) }}</p>
                            <p class="text-gray-400">{{ $m['notes'] }} {{ $m['notes'] === 1 ? 'nota' : 'notas' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <table class="sr-only">
            <caption>Faturamento bruto por mês em {{ $year }}</caption>
            <thead><tr><th>Mês</th><th>Notas</th><th>Bruto</th><th>Líquido</th></tr></thead>
            <tbody>
                @foreach($summary['months'] as $m)
                    <tr><td>{{ $competenceLabel($m['competence']) }}</td><td>{{ $m['notes'] }}</td><td>{{ $brl($m['gross']) }}</td><td>{{ $brl($m['net']) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- Fila --}}
    <section x-show="queued.length > 0" x-cloak class="bg-gray-900 border border-blue-500/30 rounded-xl p-5" aria-live="polite">
        <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
            Aguardando processamento (<span x-text="queued.length"></span>)
        </h2>
        <ul class="divide-y divide-gray-800">
            <template x-for="item in queued" :key="item.uuid">
                <li class="flex items-center justify-between gap-3 py-2 text-sm">
                    <span class="text-gray-300 truncate" x-text="item.filename"></span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-900/40 text-blue-300 whitespace-nowrap" x-text="item.status_label"></span>
                </li>
            </template>
        </ul>
    </section>

    {{-- Notas sem competência (erro de leitura) --}}
    @if($withoutCompetence->isNotEmpty())
        <section class="bg-gray-900 border border-red-500/30 rounded-xl p-5">
            <h2 class="text-lg font-semibold text-white mb-3">Requer atenção ({{ $withoutCompetence->count() }})</h2>
            <ul class="divide-y divide-gray-800">
                @foreach($withoutCompetence as $xml)
                    <li class="flex items-center justify-between gap-3 py-2 text-sm flex-wrap" data-xml="{{ $xml->uuid }}">
                        <div class="min-w-0">
                            <p class="text-gray-300 truncate">{{ $xml->filename }}</p>
                            <p class="text-xs text-red-400">{{ $xml->parse_error }}</p>
                        </div>
                        <div class="flex gap-1">
                            <button type="button" @click="retry('{{ $xml->uuid }}')" class="text-xs bg-gray-800 hover:bg-gray-700 text-gray-300 px-2.5 py-1 rounded-lg">Reprocessar</button>
                            <button type="button" @click="remove('{{ $xml->uuid }}')" class="text-xs bg-gray-800 hover:bg-gray-700 text-red-400 px-2.5 py-1 rounded-lg">Excluir</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Notas processadas por mês --}}
    <section aria-labelledby="notes-title" class="space-y-3">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 id="notes-title" class="text-lg font-semibold text-white">Notas processadas</h2>
            <form method="GET" action="{{ route('invoice-pool.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="year" value="{{ $year }}">
                <label for="pool-competence" class="text-sm text-gray-400">Competência</label>
                <select id="pool-competence" name="competence" onchange="this.form.submit()"
                        class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <option value="">Todas de {{ $year }}</option>
                    @foreach($monthNames as $num => $name)
                        <option value="{{ $year }}-{{ $num }}" @selected($competence === "{$year}-{$num}")>{{ $name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @forelse($notesByMonth as $month => $notes)
            @php
                $monthGross = $notes->where('xml_parsed', true)->sum(fn ($n) => (float) ($n->gross_amount ?? $n->amount));
                $monthNet   = $notes->where('xml_parsed', true)->sum(fn ($n) => (float) $n->amount);
                $monthErrors = $notes->where('status', 'error')->count();
            @endphp
            <details class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden group" @if($loop->first || $competence) open @endif>
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer hover:bg-gray-800/40 list-none flex-wrap">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-white font-medium">{{ $competenceLabel($month) }}</span>
                        <span class="text-xs text-gray-400">{{ $notes->count() }} {{ $notes->count() === 1 ? 'nota' : 'notas' }}</span>
                        @if($monthErrors)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-red-900/40 text-red-400">{{ $monthErrors }} com erro</span>
                        @endif
                    </div>
                    <div class="text-right text-sm">
                        <span class="text-white font-semibold">{{ $brl($monthGross) }}</span>
                        <span class="text-xs text-gray-500 ml-2">líquido {{ $brl($monthNet) }}</span>
                    </div>
                </summary>
                <div class="overflow-x-auto border-t border-gray-800">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-400 bg-gray-800/40">
                            <tr>
                                <th class="text-left font-medium px-4 py-2">Número</th>
                                <th class="text-left font-medium px-4 py-2">Tomador</th>
                                <th class="text-left font-medium px-4 py-2">Emissão</th>
                                <th class="text-right font-medium px-4 py-2">Bruto</th>
                                <th class="text-right font-medium px-4 py-2">ISS</th>
                                <th class="text-right font-medium px-4 py-2">Líquido</th>
                                <th class="text-left font-medium px-4 py-2">Fatura</th>
                                <th class="px-4 py-2"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($notes as $xml)
                                <tr class="hover:bg-gray-800/30" data-xml="{{ $xml->uuid }}">
                                    <td class="px-4 py-2.5 align-top">
                                        <p class="text-white">{{ $xml->invoice_number ?? '—' }}</p>
                                        @if($xml->access_key)
                                            <button type="button" @click="copy('{{ $xml->access_key }}')" class="text-[11px] font-mono text-gray-500 hover:text-gray-300" title="Copiar chave {{ $xml->access_key }}">
                                                {{ Str::limit($xml->access_key, 16, '…') }}
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 align-top">
                                        <p class="text-gray-300">{{ $xml->company?->name ?? $xml->recipient_name ?? '—' }}</p>
                                        <p class="text-[11px] font-mono text-gray-500">{{ $xml->recipient_cnpj }}</p>
                                    </td>
                                    <td class="px-4 py-2.5 align-top text-gray-300 whitespace-nowrap">{{ $xml->issued_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-4 py-2.5 align-top text-right text-gray-300 whitespace-nowrap">{{ $xml->gross_amount !== null ? $brl($xml->gross_amount) : '—' }}</td>
                                    <td class="px-4 py-2.5 align-top text-right text-gray-400 whitespace-nowrap">
                                        {{ $xml->iss_amount !== null ? $brl($xml->iss_amount) : '—' }}
                                        @if($xml->iss_retained)<span class="block text-[10px] text-yellow-400">retido</span>@endif
                                    </td>
                                    <td class="px-4 py-2.5 align-top text-right text-white font-medium whitespace-nowrap">{{ $xml->amount !== null ? $brl($xml->amount) : '—' }}</td>
                                    <td class="px-4 py-2.5 align-top">
                                        @if($xml->status === 'processed' && $xml->invoice)
                                            <a href="{{ route('invoices.show', $xml->invoice->uuid) }}" class="text-emerald-400 hover:underline">{{ $xml->invoice->title }}</a>
                                            @if($xml->invoice->billingGroup)
                                                <p class="text-[11px] text-gray-500">Grupo: {{ $xml->invoice->billingGroup->name }}</p>
                                            @endif
                                        @elseif($xml->status === 'error')
                                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-red-900/40 text-red-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                                Requer atenção
                                            </span>
                                            <p class="text-xs text-red-400 mt-1 max-w-xs">{{ $xml->parse_error }}</p>
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 align-top">
                                        <div class="flex items-center justify-end gap-1">
                                            @if($xml->status === 'error')
                                                <button type="button" @click="retry('{{ $xml->uuid }}')" class="p-1.5 text-gray-400 hover:text-blue-400 rounded-lg" title="Reprocessar" aria-label="Reprocessar nota">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                </button>
                                            @endif
                                            @if($xml->supportsDanfse())
                                                <a href="{{ route('invoice-pool.danfse', $xml->uuid) }}" target="_blank" rel="noopener" class="p-1.5 text-gray-400 hover:text-cyan-400 rounded-lg" title="Imprimir DANFSe" aria-label="Imprimir DANFSe da nota {{ $xml->invoice_number }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('invoice-pool.download', $xml->uuid) }}" class="p-1.5 text-gray-400 hover:text-white rounded-lg" title="Baixar XML" aria-label="Baixar XML">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                            @if(!$xml->invoice?->isClosed())
                                                <button type="button" @click="remove('{{ $xml->uuid }}')" class="p-1.5 text-gray-400 hover:text-red-400 rounded-lg" title="Excluir" aria-label="Excluir nota">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @empty
            <div class="text-center py-12 text-gray-500 bg-gray-900 border border-gray-800 rounded-xl">
                <p class="text-gray-400 font-medium mb-1">Nenhuma nota processada {{ $competence ? 'nesta competência' : 'em ' . $year }}</p>
                <p class="text-sm">Envie os XMLs acima para começar.</p>
            </div>
        @endforelse
    </section>
</div>

<script>
function invoicePoolPage() {
    return {
        queued: @json($queuedPayload),
        uploading: false,
        editingLimit: false,
        limit: @json($summary['limit']),
        pollTimer: null,

        init() {
            if (this.queued.length) this.startPolling();
        },

        onDrop(event) {
            this.upload(event.dataTransfer.files);
        },

        async upload(fileList) {
            const files = Array.from(fileList || []).filter(f => f.name.toLowerCase().endsWith('.xml'));
            if (!files.length) { showToast('Selecione arquivos XML.', TOAST_TYPES.WARNING); return; }
            if (files.length > 50) { showToast('Envie no máximo 50 arquivos por vez.', TOAST_TYPES.WARNING); return; }

            const form = new FormData();
            files.forEach(f => form.append('xmls[]', f));

            this.uploading = true;
            try {
                const res = await fetch('{{ route('invoice-pool.store') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: form,
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'Erro ao enviar notas.');
                this.queued.push(...data.xmls);
                showToast(data.message, TOAST_TYPES.SUCCESS);
                this.startPolling();
            } catch (e) {
                showToast(e.message, TOAST_TYPES.ERROR);
            } finally {
                this.uploading = false;
            }
        },

        startPolling() {
            if (this.pollTimer) return;
            this.pollTimer = setInterval(() => this.poll(), 5000);
        },

        async poll() {
            const params = new URLSearchParams();
            this.queued.forEach(q => params.append('uuids[]', q.uuid));
            try {
                const res = await fetch('{{ route('invoice-pool.status') }}?' + params, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const byUuid = Object.fromEntries(data.xmls.map(x => [x.uuid, x]));
                this.queued = this.queued.map(q => byUuid[q.uuid] || q).filter(q => ['pending', 'processing'].includes(q.status));
                if (data.pending === 0) {
                    clearInterval(this.pollTimer);
                    this.pollTimer = null;
                    showToast('Processamento concluído. Atualizando...', TOAST_TYPES.INFO);
                    setTimeout(() => location.reload(), 800);
                }
            } catch (e) { /* tenta de novo no próximo ciclo */ }
        },

        async retry(uuid) {
            await this.request(`/invoice-pool/${uuid}/retry`, 'POST', null, (data) => {
                document.querySelectorAll(`[data-xml="${uuid}"]`).forEach(el => el.remove());
                this.queued.push(data.xml);
                this.startPolling();
            });
        },

        remove(uuid) {
            showConfirm('Deseja realmente excluir esta nota? O arquivo XML será apagado.', () => {
                this.request(`/invoice-pool/${uuid}`, 'DELETE', null, () => {
                    document.querySelectorAll(`[data-xml="${uuid}"]`).forEach(el => el.remove());
                });
            }, 'Excluir Nota');
        },

        async saveLimit() {
            const value = this.limit === '' || this.limit === null ? null : Number(this.limit);
            await this.request('{{ route('invoice-pool.revenue-limit') }}', 'PUT', { annual_revenue_limit: value }, () => location.reload());
        },

        async copy(text) {
            try {
                await navigator.clipboard.writeText(text);
                showToast('Chave copiada.', TOAST_TYPES.SUCCESS);
            } catch (e) {
                showToast('Não foi possível copiar.', TOAST_TYPES.ERROR);
            }
        },

        async request(url, method, body, onSuccess) {
            try {
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: body ? JSON.stringify(body) : null,
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erro na operação.');
                showToast(data.message, TOAST_TYPES.SUCCESS);
                onSuccess(data);
            } catch (e) {
                showToast(e.message, TOAST_TYPES.ERROR);
            }
        },
    };
}
</script>
</x-app-layout>
