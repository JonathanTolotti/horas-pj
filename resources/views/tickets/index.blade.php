<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-white">Meus Chamados</h1>
                <a href="{{ route('tickets.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Abrir Chamado
                </a>
            </div>

            @if(session('success'))
                <div class="px-4 py-3 bg-emerald-900/40 border border-emerald-700 text-emerald-300 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tabela --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                @if($tickets->isEmpty())
                    <div class="text-center py-16 text-gray-500">
                        <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Nenhum chamado aberto ainda.
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-800">
                            <tr class="text-gray-500 text-xs uppercase tracking-wider">
                                <th class="px-5 py-3 text-left font-medium">#</th>
                                <th class="px-5 py-3 text-left font-medium">Título</th>
                                <th class="px-5 py-3 text-left font-medium hidden md:table-cell">Categoria</th>
                                <th class="px-5 py-3 text-left font-medium">Status</th>
                                <th class="px-5 py-3 text-left font-medium hidden lg:table-cell">Responsável</th>
                                <th class="px-5 py-3 text-left font-medium hidden lg:table-cell">Atualização</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-gray-800/50 cursor-pointer transition-colors"
                                    onclick="window.location='{{ route('tickets.show', $ticket) }}'">
                                    <td class="px-5 py-3 text-gray-500">{{ $ticket->id }}</td>
                                    <td class="px-5 py-3 max-w-xs">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-white font-medium truncate">{{ $ticket->title }}</span>
                                            @if($ticket->has_unread_reply)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                    Nova resposta
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-400 hidden md:table-cell">{{ $ticket->category->label() }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $ticket->status->badgeClasses() }}">
                                            {{ $ticket->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-400 hidden lg:table-cell">
                                        {{ $ticket->operator?->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 text-xs hidden lg:table-cell">
                                        {{ $ticket->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
