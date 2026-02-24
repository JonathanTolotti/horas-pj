@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Usuários</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $users->total() }} usuário(s) encontrado(s)</p>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Buscar por nome ou e-mail..."
                class="w-full pl-9 pr-4 py-2.5 bg-gray-900 border border-gray-700 rounded-lg text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"
            >
        </div>
        <select
            name="plan"
            onchange="this.form.submit()"
            class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-cyan-500"
        >
            <option value="">Todos os planos</option>
            <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
            <option value="trial"   {{ request('plan') === 'trial'   ? 'selected' : '' }}>Trial</option>
            <option value="free"    {{ request('plan') === 'free'    ? 'selected' : '' }}>Gratuito</option>
        </select>
        <button type="submit"
                class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium rounded-lg transition-colors">
            Buscar
        </button>
        @if(request('q') || request('plan'))
            <a href="{{ route('admin.users') }}"
               class="px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                Limpar
            </a>
        @endif
    </form>

    <!-- Tabela -->
    <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
        @if($users->isEmpty())
            <div class="text-center py-16 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <p>Nenhum usuário encontrado.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Usuário</th>
                            <th class="px-4 py-3 text-left">Plano</th>
                            <th class="px-4 py-3 text-left hidden sm:table-cell">Cadastro</th>
                            <th class="px-4 py-3 text-left hidden lg:table-cell">Admin</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @foreach($users as $user)
                            @php
                                $sub = $user->subscription;
                                if ($sub && $sub->status === 'active' && $sub->ends_at?->isFuture()) {
                                    $planLabel = 'Premium';
                                    $planClass = 'bg-green-500/20 text-green-300';
                                } elseif ($sub && $sub->status === 'trial' && $sub->trial_ends_at?->isFuture()) {
                                    $planLabel = 'Trial';
                                    $planClass = 'bg-yellow-500/20 text-yellow-300';
                                } else {
                                    $planLabel = 'Gratuito';
                                    $planClass = 'bg-gray-500/20 text-gray-400';
                                }
                            @endphp
                            <tr class="text-gray-300 hover:bg-gray-800/30 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $planClass }}">
                                        {{ $planLabel }}
                                    </span>
                                    @if($sub && $sub->status === 'active' && $sub->ends_at?->isFuture())
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            até {{ $sub->ends_at->format('d/m/Y') }}
                                        </div>
                                    @elseif($sub && $sub->status === 'trial' && $sub->trial_ends_at?->isFuture())
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            até {{ $sub->trial_ends_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs hidden sm:table-cell">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    @if($user->is_admin)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300">Admin</span>
                                    @else
                                        <span class="text-xs text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white text-xs font-medium rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-4 py-3 border-t border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
