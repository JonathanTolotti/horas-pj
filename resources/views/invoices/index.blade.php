<x-app-layout>
<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="invoicesPage()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Faturas</h1>
            <p class="text-gray-400 text-sm mt-0.5">Gerencie suas faturas e notas fiscais</p>
        </div>
        <button @click="openNewInvoice()"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Fatura
        </button>
    </div>

    {{-- Filtros --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-36">
                <label class="block text-xs text-gray-400 mb-1">Competência</label>
                <input type="month" name="reference_month"
                       value="{{ request('reference_month') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
            </div>
            <div class="flex-1 min-w-36">
                <label class="block text-xs text-gray-400 mb-1">Status</label>
                <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-36">
                <label class="block text-xs text-gray-400 mb-1">Empresa</label>
                <select name="company_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <option value="">Todas</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected(request('company_id') == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-36">
                <label class="block text-xs text-gray-400 mb-1">Conta Bancária</label>
                <select name="bank_account_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <option value="">Todas</option>
                    @foreach($bankAccounts as $ba)
                        <option value="{{ $ba->uuid }}" @selected(request('bank_account_id') === $ba->uuid)>{{ $ba->bank_name }} — {{ $ba->account_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                    Filtrar
                </button>
                @if(request()->hasAny(['reference_month','status','company_id','bank_account_id']))
                    <a href="{{ route('invoices.index') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-400 text-sm px-4 py-2 rounded-lg transition-colors">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Lista de Faturas --}}
    @if($invoices->isEmpty())
        <div class="text-center py-16 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-lg font-medium">Nenhuma fatura encontrada</p>
            <p class="text-sm mt-1">Crie sua primeira fatura clicando no botão acima.</p>
        </div>
    @else
        <div class="grid gap-3">
            @foreach($invoices as $invoice)
            <a href="{{ route('invoices.show', $invoice->uuid) }}"
               class="block bg-gray-900 border border-gray-800 hover:border-gray-600 rounded-xl p-4 transition-colors group">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-white font-medium group-hover:text-emerald-400 transition-colors truncate">
                                {{ $invoice->title }}
                            </span>
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
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$invoice->status] }}">
                                {{ $statusLabels[$invoice->status] }}
                            </span>
                            @php $rec = $invoice->getReconciliationStatus(); @endphp
                            @if($rec === 'conciliado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-900/30 text-emerald-400">
                                    ✓ Conciliado
                                </span>
                            @elseif($rec === 'parcial')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-900/30 text-yellow-400">
                                    ⚠ Parcial
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-1 text-sm text-gray-400 flex-wrap">
                            <span>{{ \Carbon\Carbon::parse($invoice->reference_month . '-01')->translatedFormat('F Y') }}</span>
                            @if($invoice->company)
                                <span class="text-gray-600">·</span>
                                <span>{{ $invoice->company->name }}</span>
                            @endif
                            @if($invoice->bankAccount)
                                <span class="text-gray-600">·</span>
                                <span>{{ $invoice->bankAccount->bank_name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        @php $net = $invoice->getNetTotal(); @endphp
                        <p class="text-lg font-semibold {{ $net >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            R$ {{ number_format(abs($net), 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500">líquido</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif

    {{-- Modal Nova Fatura --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
         @click.self="showModal = false"
         style="display:none">
        <div class="bg-gray-900 border border-gray-700 rounded-xl w-full max-w-lg shadow-2xl"
             @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <h2 class="text-lg font-semibold text-white">Nova Fatura</h2>
                <button @click="showModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form @submit.prevent="submitInvoice()" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Título <span class="text-red-400">*</span></label>
                    <input type="text" x-model="form.title" placeholder="Ex: Serviços de TI – Março/2026"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <p x-show="errors.title" x-text="errors.title" class="text-red-400 text-xs mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Competência <span class="text-red-400">*</span></label>
                    <input type="month" x-model="form.reference_month"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    <p x-show="errors.reference_month" x-text="errors.reference_month" class="text-red-400 text-xs mt-1"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Empresa</label>
                        <select x-model="form.company_id"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                            <option value="">Nenhuma</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Conta Bancária</label>
                        <select x-model="form.bank_account_id"
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
                    <textarea x-model="form.notes" rows="2" placeholder="Opcional..."
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="loading"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                        <span x-show="!loading">Criar Fatura</span>
                        <span x-show="loading">Criando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function invoicesPage() {
    return {
        showModal: false,
        loading: false,
        form: { title: '', reference_month: '', company_id: '', bank_account_id: '', notes: '' },
        errors: {},

        openNewInvoice() {
            this.form = {
                title: '',
                reference_month: new Date().toISOString().slice(0, 7),
                company_id: '',
                bank_account_id: '',
                notes: '',
            };
            this.errors = {};
            this.showModal = true;
        },

        async submitInvoice() {
            this.loading = true;
            this.errors = {};
            try {
                const res = await fetch('/invoices', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (data.success) {
                    window.location.href = data.redirect;
                } else if (data.errors) {
                    this.errors = data.errors;
                } else {
                    showToast(data.message || 'Erro ao criar fatura.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao criar fatura.', TOAST_TYPES.ERROR);
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
</div>
</x-app-layout>
