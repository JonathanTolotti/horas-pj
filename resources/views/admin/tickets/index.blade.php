@extends('layouts.admin')

@section('title', 'Chamados de Suporte')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-white">Chamados de Suporte</h1>
    </div>

    {{-- Cards por status --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statuses as $status)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                <p class="text-gray-400 text-xs mb-1">{{ $status->label() }}</p>
                <p class="text-2xl font-bold text-white">{{ $counts[$status->value] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3">
        <select name="status" onchange="this.form.submit()"
                class="bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-cyan-500">
            <option value="">Todos os status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        <select name="category" onchange="this.form.submit()"
                class="bg-gray-800 border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-cyan-500">
            <option value="">Todas as categorias</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->value }}" {{ request('category') === $cat->value ? 'selected' : '' }}>
                    {{ $cat->label() }}
                </option>
            @endforeach
        </select>
        @if(request('status') || request('category'))
            <a href="{{ route('admin.tickets.index') }}"
               class="px-3 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                Limpar filtros
            </a>
        @endif
    </form>

    {{-- Tabela --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        @if($tickets->isEmpty())
            <div class="text-center py-16 text-gray-500">Nenhum chamado encontrado.</div>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-gray-800">
                    <tr class="text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-5 py-3 text-left font-medium">#</th>
                        <th class="px-5 py-3 text-left font-medium">Usuário</th>
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
                            onclick="window.location='{{ route('admin.tickets.show', $ticket) }}'">
                            <td class="px-5 py-3 text-gray-500">{{ $ticket->id }}</td>
                            <td class="px-5 py-3 text-gray-300">{{ $ticket->user->name }}</td>
                            <td class="px-5 py-3 text-white font-medium max-w-xs truncate">{{ $ticket->title }}</td>
                            <td class="px-5 py-3 text-gray-400 hidden md:table-cell">{{ $ticket->category->label() }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $ticket->status->badgeClasses() }}">
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-400 hidden lg:table-cell">
                                {{ $ticket->operator?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-gray-500 hidden lg:table-cell text-xs">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($tickets->hasPages())
                <div class="px-5 py-4 border-t border-gray-800">
                    {{ $tickets->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
