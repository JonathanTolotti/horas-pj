@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="text-sm text-gray-400 mt-1">Visão geral do produto</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <!-- Total Usuários -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-400 font-medium">Total de Usuários</span>
                <div class="w-9 h-9 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
        </div>

        <!-- Premium Ativos -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-400 font-medium">Premium Ativos</span>
                <div class="w-9 h-9 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['premium'] }}</p>
        </div>

        <!-- Em Trial -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-400 font-medium">Em Trial</span>
                <div class="w-9 h-9 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['trial'] }}</p>
        </div>

        <!-- Gratuitos -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-400 font-medium">Gratuitos</span>
                <div class="w-9 h-9 rounded-lg bg-gray-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['gratuitos'] }}</p>
        </div>

        <!-- Novos 30d -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-400 font-medium">Novos (30 dias)</span>
                <div class="w-9 h-9 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['new_30d'] }}</p>
        </div>

        <!-- Ativos 30d -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-400 font-medium">Ativos (30 dias)</span>
                <div class="w-9 h-9 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['active_30d'] }}</p>
        </div>

        <!-- Receita Total -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-5 sm:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-400 font-medium">Receita Total</span>
                <div class="w-9 h-9 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">
                R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}
            </p>
        </div>

    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.users') }}"
           class="flex items-center gap-3 bg-gray-900 border border-gray-700 hover:border-cyan-600 rounded-xl p-4 transition-colors group">
            <div class="w-10 h-10 rounded-lg bg-cyan-600/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-white group-hover:text-cyan-400 transition-colors">Gerenciar Usuários</p>
                <p class="text-xs text-gray-500">Buscar, ver detalhes, ativar premium</p>
            </div>
        </a>
        <a href="{{ route('admin.changelogs.index') }}"
           class="flex items-center gap-3 bg-gray-900 border border-gray-700 hover:border-cyan-600 rounded-xl p-4 transition-colors group">
            <div class="w-10 h-10 rounded-lg bg-cyan-600/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-white group-hover:text-cyan-400 transition-colors">Changelog</p>
                <p class="text-xs text-gray-500">Publicar novidades para usuários</p>
            </div>
        </a>
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 bg-gray-900 border border-gray-700 hover:border-gray-600 rounded-xl p-4 transition-colors group">
            <div class="w-10 h-10 rounded-lg bg-gray-700/40 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-white group-hover:text-gray-300 transition-colors">Voltar ao App</p>
                <p class="text-xs text-gray-500">Ir para o dashboard de usuário</p>
            </div>
        </a>
    </div>

</div>
@endsection
