<x-app-layout>
    <div class="max-w-5xl mx-auto p-4 sm:p-6 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Acompanhando</h1>
                <p class="text-gray-400 text-sm mt-1">Dados de horas que você tem acesso para visualizar</p>
            </div>
            <a href="{{ route('supervisor.invitations') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white rounded-lg text-sm transition-colors border border-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Convites recebidos
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-900/40 border border-red-500/50 text-red-200 rounded-lg px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($supervisedData->isEmpty())
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
                <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-400 text-lg font-medium">Nada por aqui ainda</p>
                <p class="text-gray-500 text-sm mt-2">Quando alguém te convidar como supervisor, você verá os dados aqui.</p>
                <a href="{{ route('supervisor.invitations') }}"
                   class="inline-flex items-center gap-2 mt-5 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm transition-colors">
                    Ver convites recebidos
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($supervisedData as $item)
                    @php
                        $access = $item['access'];
                        $user = $item['user'];
                        $hours = $item['total_hours'];
                        $h = floor($hours);
                        $m = round(($hours - $h) * 60);
                    @endphp
                    <a href="{{ route('supervisor.show', $access->uuid) }}"
                       class="bg-gray-900 border border-gray-800 hover:border-cyan-500/50 rounded-xl p-5 transition-colors group block">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <svg class="w-4 h-4 text-gray-600 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <h3 class="font-semibold text-white group-hover:text-cyan-300 transition-colors">{{ $user->name }}</h3>
                        <p class="text-gray-400 text-sm truncate">{{ $user->email }}</p>

                        <div class="mt-3 pt-3 border-t border-gray-800 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">Horas este mês</p>
                                <p class="text-white font-mono font-medium">{{ sprintf('%02d:%02d', $h, $m) }}</p>
                            </div>
                            @if($item['last_entry_date'])
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Último lançamento</p>
                                <p class="text-gray-400 text-sm">{{ $item['last_entry_date']->format('d/m/Y') }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="flex gap-1.5 mt-3 flex-wrap">
                            @if($access->can_view_financials)
                                <span title="Valores financeiros" class="text-xs px-1.5 py-0.5 bg-emerald-900/40 text-emerald-400 rounded">Financeiro</span>
                            @endif
                            @if($access->can_view_analytics)
                                <span title="Analytics" class="text-xs px-1.5 py-0.5 bg-purple-900/40 text-purple-400 rounded">Analytics</span>
                            @endif
                            @if($access->can_export)
                                <span title="Exportação" class="text-xs px-1.5 py-0.5 bg-indigo-900/40 text-indigo-400 rounded">Exportar</span>
                            @endif

                            @if($access->expires_at)
                                <span class="ml-auto text-xs text-gray-500">
                                    até {{ $access->expires_at->format('d/m') }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
