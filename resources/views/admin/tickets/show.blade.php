@extends('layouts.admin')

@section('title', 'Chamado #' . $ticket->id)

@section('content')
<div class="space-y-6" x-data="{ statusValue: '{{ $ticket->status->value }}' }">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.tickets.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Chamados</a>
                <span class="text-gray-700">/</span>
                <span class="text-gray-300 text-sm">#{{ $ticket->id }}</span>
            </div>
            <h1 class="text-xl font-bold text-white">{{ $ticket->title }}</h1>
        </div>
        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full border shrink-0 {{ $ticket->status->badgeClasses() }}">
            {{ $ticket->status->label() }}
        </span>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-emerald-900/40 border border-emerald-700 text-emerald-300 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="px-4 py-3 bg-blue-900/40 border border-blue-700 text-blue-300 rounded-lg text-sm">
            {{ session('info') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Thread + Resposta --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Mensagens --}}
            <div class="space-y-3">
                @foreach($ticket->messages as $message)
                    @php $isOperator = $message->user->is_admin; @endphp
                    <div @class([
                        'rounded-xl px-4 py-3 border',
                        'bg-yellow-900/20 border-yellow-700/40' => $message->is_internal,
                        'bg-gray-900 border-gray-800' => !$message->is_internal && !$isOperator,
                        'bg-cyan-900/20 border-cyan-800/40' => !$message->is_internal && $isOperator,
                    ])>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span @class([
                                'text-xs font-semibold',
                                'text-yellow-400' => $message->is_internal,
                                'text-cyan-400' => !$message->is_internal && $isOperator,
                                'text-gray-300' => !$message->is_internal && !$isOperator,
                            ])>
                                {{ $message->user->name }}
                            </span>
                            @if($message->is_internal)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-600/40">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Nota Interna
                                </span>
                            @endif
                            <span class="text-gray-600 text-xs">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">{{ $message->body }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Formulário de resposta --}}
            <div x-data="{ isInternal: false }"
                 :class="isInternal ? 'border-yellow-700/60 bg-yellow-900/10' : 'border-gray-800 bg-gray-900'"
                 class="rounded-xl border p-4 transition-colors">
                <form method="POST" action="{{ route('admin.tickets.messages.store', $ticket) }}">
                    @csrf
                    <input type="hidden" name="is_internal" :value="isInternal ? 1 : 0">

                    <textarea name="body" rows="4"
                              :placeholder="isInternal ? 'Nota interna (não visível para o cliente)...' : 'Escreva uma resposta para o cliente...'"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-cyan-500 transition-colors resize-none mb-3"></textarea>

                    <div class="flex items-center justify-between gap-3">
                        <button type="button" @click="isInternal = !isInternal"
                                :class="isInternal ? 'bg-yellow-600/30 text-yellow-400 border-yellow-600/50' : 'bg-gray-800 text-gray-400 border-gray-700'"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span x-text="isInternal ? 'Nota Interna ativada' : 'Nota Interna'"></span>
                        </button>
                        <button type="submit"
                                :class="isInternal ? 'bg-yellow-600 hover:bg-yellow-500' : 'bg-cyan-600 hover:bg-cyan-500'"
                                class="px-5 py-2 text-white text-sm font-medium rounded-lg transition-colors">
                            <span x-text="isInternal ? 'Salvar Nota' : 'Enviar Resposta'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Painel lateral --}}
        <div class="space-y-4">

            {{-- Info do chamado --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 space-y-3 text-sm">
                <h3 class="text-white font-semibold text-xs uppercase tracking-wider text-gray-500">Detalhes</h3>
                <div>
                    <p class="text-gray-500 text-xs">Cliente</p>
                    <p class="text-gray-200">{{ $ticket->user->name }}</p>
                    <p class="text-gray-500 text-xs">{{ $ticket->user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Categoria</p>
                    <p class="text-gray-200">{{ $ticket->category->label() }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Aberto em</p>
                    <p class="text-gray-200">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Última atualização</p>
                    <p class="text-gray-200">{{ $ticket->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 space-y-3">
                <h3 class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Status</h3>
                <select x-model="statusValue" @change="updateStatus(statusValue)"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-cyan-500">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Responsável --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 space-y-3">
                <h3 class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Responsável</h3>
                @if($ticket->operator)
                    <p class="text-gray-200 text-sm">{{ $ticket->operator->name }}</p>
                @else
                    <p class="text-gray-500 text-sm">Sem responsável</p>
                @endif
                @if($ticket->operator_id !== auth()->id())
                    <form method="POST" action="{{ route('admin.tickets.assign', $ticket) }}">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            Assumir chamado
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    fetch('{{ route('admin.tickets.status', $ticket) }}', {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) showToast('Status atualizado para "' + d.label + '"', TOAST_TYPES.SUCCESS);
    })
    .catch(() => showToast('Erro ao atualizar status', TOAST_TYPES.ERROR));
}
</script>
@endsection
