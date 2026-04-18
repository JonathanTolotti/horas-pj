<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header / breadcrumb --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('tickets.index') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Chamados</a>
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

            @if($errors->any())
                <div class="px-4 py-3 bg-red-900/40 border border-red-700 text-red-300 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-6">

                {{-- Thread + Resposta --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Mensagens --}}
                    <div class="space-y-3">
                        @foreach($ticket->publicMessages as $message)
                            @php $isOwn = $message->user_id === auth()->id(); @endphp
                            <div @class([
                                'rounded-xl px-4 py-3 border',
                                'bg-cyan-900/20 border-cyan-800/40' => $isOwn,
                                'bg-gray-900 border-gray-800' => !$isOwn,
                            ])>
                                <div class="flex items-center gap-2 mb-2">
                                    <span @class([
                                        'text-xs font-semibold',
                                        'text-cyan-400' => $isOwn,
                                        'text-gray-300' => !$isOwn,
                                    ])>
                                        {{ $isOwn ? 'Você' : $message->user->name }}
                                    </span>
                                    <span class="text-gray-600 text-xs">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">{{ $message->body }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Formulário de resposta --}}
                    @if(!$ticket->isClosed())
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                            <form method="POST" action="{{ route('tickets.messages.store', $ticket) }}">
                                @csrf
                                <textarea name="body" rows="4"
                                          placeholder="Escreva sua mensagem..."
                                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-cyan-500 transition-colors resize-none mb-3"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg transition-colors">
                                        Enviar Mensagem
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-600 text-sm">
                            Este chamado está encerrado.
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-4">

                    {{-- Detalhes --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 space-y-3 text-sm">
                        <h3 class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Detalhes</h3>
                        <div>
                            <p class="text-gray-500 text-xs">Categoria</p>
                            <p class="text-gray-200">{{ $ticket->category->label() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Responsável</p>
                            <p class="text-gray-200">{{ $ticket->operator?->name ?? 'Sem responsável' }}</p>
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

                    {{-- Encerrar --}}
                    @if(!$ticket->isClosed())
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                            <form id="close-ticket-form" method="POST" action="{{ route('tickets.close', $ticket) }}">
                                @csrf
                                <button type="button"
                                        onclick="showConfirm('Deseja encerrar este chamado? Ele não aceitará novas mensagens.', () => document.getElementById('close-ticket-form').submit(), 'Encerrar chamado', 'Encerrar')"
                                        class="w-full px-4 py-2 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                    Encerrar chamado
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
