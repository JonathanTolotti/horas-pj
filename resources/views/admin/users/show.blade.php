@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users') }}"
           class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $user->email }}</p>
        </div>
        @if($user->is_admin)
            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-cyan-500/20 text-cyan-300">Admin</span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Coluna Esquerda -->
        <div class="lg:col-span-1 space-y-4">

            <!-- Info Básica -->
            <div class="bg-gray-900 border border-gray-700 rounded-xl p-5 space-y-3">
                <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Informações</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-300 font-mono">{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nome</span>
                        <span class="text-gray-300">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">E-mail</span>
                        <span class="text-gray-300 break-all">{{ $user->email }}</span>
                    </div>
                    @if($user->phone)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Telefone</span>
                        <span class="text-gray-300">{{ $user->phone }}</span>
                    </div>
                    @endif
                    @if($user->tax_id)
                    <div class="flex justify-between">
                        <span class="text-gray-500">CPF/CNPJ</span>
                        <span class="text-gray-300">{{ $user->tax_id }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Cadastro</span>
                        <span class="text-gray-300">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Plano Atual -->
            @php
                $sub = $user->subscription;
                if ($sub && $sub->status === 'active' && $sub->ends_at?->isFuture()) {
                    $planLabel = 'Premium';
                    $planClass = 'text-green-400';
                } elseif ($sub && $sub->status === 'trial' && $sub->trial_ends_at?->isFuture()) {
                    $planLabel = 'Trial';
                    $planClass = 'text-yellow-400';
                } else {
                    $planLabel = 'Gratuito';
                    $planClass = 'text-gray-400';
                }
            @endphp
            <div class="bg-gray-900 border border-gray-700 rounded-xl p-5 space-y-3">
                <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Plano</h2>
                <p class="text-xl font-bold {{ $planClass }}">{{ $planLabel }}</p>
                @if($sub)
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span class="text-gray-300">{{ $sub->status }}</span>
                        </div>
                        @if($sub->ends_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Expira em</span>
                            <span class="text-gray-300">{{ $sub->ends_at->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        @if($sub->trial_ends_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Trial até</span>
                            <span class="text-gray-300">{{ $sub->trial_ends_at->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Ações -->
            <div class="bg-gray-900 border border-gray-700 rounded-xl p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Ações</h2>

                <!-- Ativar Premium -->
                <div x-data="{ months: 1, loading: false }">
                    <p class="text-xs text-gray-400 mb-2">Ativar / Estender Premium</p>
                    <div class="flex gap-2">
                        <select
                            x-model="months"
                            class="flex-1 bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-cyan-500"
                        >
                            <option value="1">1 mês</option>
                            <option value="3">3 meses</option>
                            <option value="6">6 meses</option>
                            <option value="12">12 meses</option>
                        </select>
                        <button
                            @click="activatePremium({{ $user->id }}, months)"
                            :disabled="loading"
                            class="px-3 py-2 bg-cyan-600 hover:bg-cyan-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <span x-show="!loading">Ativar</span>
                            <span x-show="loading" x-cloak>...</span>
                        </button>
                    </div>
                </div>

                <!-- Toggle Admin -->
                <div>
                    <p class="text-xs text-gray-400 mb-2">Permissão Admin</p>
                    <button
                        onclick="toggleAdmin({{ $user->id }}, this)"
                        data-is-admin="{{ $user->is_admin ? 'true' : 'false' }}"
                        class="w-full px-3 py-2 {{ $user->is_admin ? 'bg-red-700 hover:bg-red-800' : 'bg-gray-700 hover:bg-gray-600' }} text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        {{ $user->is_admin ? 'Remover Admin' : 'Tornar Admin' }}
                    </button>
                </div>

                <div class="border-t border-gray-700 pt-4"></div>

                <!-- Cota de Armazenamento -->
                <div x-data="{ quotaMb: {{ round($storageData['quota_bytes'] / 1048576) }}, loading: false }">
                    <p class="text-xs text-gray-400 mb-1">Cota de Armazenamento</p>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                        <span class="{{ $storageData['text_color'] }} font-medium">{{ $storageData['used_mb'] }} MB</span>
                        <span>/ {{ $storageData['quota_mb'] }} MB usados</span>
                        <span class="ml-auto {{ $storageData['text_color'] }}">{{ $storageData['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-1.5 mb-3">
                        <div class="h-1.5 rounded-full {{ $storageData['color_class'] }} transition-all"
                             style="width: {{ $storageData['percentage'] }}%"></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <input
                                type="number"
                                x-model="quotaMb"
                                min="1"
                                max="102400"
                                class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 pr-10"
                            >
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">MB</span>
                        </div>
                        <button
                            @click="updateStorageQuota({{ $user->id }}, quotaMb)"
                            :disabled="loading"
                            class="px-3 py-2 bg-gray-700 hover:bg-gray-600 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors whitespace-nowrap"
                        >
                            <span x-show="!loading">Salvar</span>
                            <span x-show="loading" x-cloak>...</span>
                        </button>
                    </div>
                </div>

                <!-- 2FA -->
                <div>
                    <p class="text-xs text-gray-400 mb-2">Autenticação de Dois Fatores</p>
                    @if($user->two_factor_enabled)
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300">Ativo</span>
                        </div>
                        <button
                            onclick="disable2fa({{ $user->id }}, this)"
                            class="w-full px-3 py-2 bg-orange-700 hover:bg-orange-800 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            Desativar 2FA
                        </button>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-700 text-gray-400">Inativo</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Stats Rápidas -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-white">{{ $totalEntries }}</p>
                    <p class="text-xs text-gray-500 mt-1">Lançamentos</p>
                </div>
                <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 text-center">
                    @php
                        $h = floor($totalHours);
                        $m = round(($totalHours - $h) * 60);
                    @endphp
                    <p class="text-2xl font-bold text-white">{{ sprintf('%02d:%02d', $h, $m) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total de Horas</p>
                </div>
                <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-white">{{ $payments->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Pagamentos</p>
                </div>
            </div>

            <!-- Histórico de Pagamentos -->
            <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-300">Histórico de Pagamentos</h2>
                    @if($payments->isNotEmpty())
                        @php $totalPago = $payments->where('status', 'paid')->sum('amount'); @endphp
                        <span class="text-xs text-gray-500">
                            Total recebido:
                            <span class="text-emerald-400 font-semibold">R$ {{ number_format($totalPago, 2, ',', '.') }}</span>
                        </span>
                    @endif
                </div>
                @if($payments->isEmpty())
                    <div class="py-8 text-center text-gray-600 text-sm">Nenhum pagamento registrado.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3 text-left w-10">#</th>
                                    <th class="px-4 py-3 text-left">Criado em</th>
                                    <th class="px-4 py-3 text-left">Valor</th>
                                    <th class="px-4 py-3 text-left">Meses</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left hidden md:table-cell">Pago em</th>
                                    <th class="px-4 py-3 w-6"></th>
                                </tr>
                            </thead>
                                @foreach($payments as $payment)
                                    @php
                                        $statusClass = match($payment->status) {
                                            'paid'    => 'bg-green-500/20 text-green-300',
                                            'pending' => 'bg-yellow-500/20 text-yellow-300',
                                            default   => 'bg-gray-500/20 text-gray-400',
                                        };
                                        $statusLabel = match($payment->status) {
                                            'paid'    => 'Pago',
                                            'pending' => 'Pendente',
                                            'expired' => 'Expirado',
                                            default   => $payment->status,
                                        };
                                    @endphp
                                    <tbody x-data="{ open: false }" class="border-t border-gray-700/50">
                                        {{-- Linha resumo --}}
                                        <tr class="text-gray-300 hover:bg-gray-800/30 transition-colors cursor-pointer"
                                            @click="open = !open">
                                            <td class="px-4 py-3 text-xs text-gray-600 font-mono">{{ $payment->id }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-400">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-3 font-semibold text-white">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-400">{{ $payment->months }}x</td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs px-2 py-0.5 rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-400 hidden md:table-cell">
                                                {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <svg class="w-4 h-4 text-gray-500 transition-transform inline-block"
                                                     :class="open ? 'rotate-180' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </td>
                                        </tr>
                                        {{-- Linha de detalhes expandida --}}
                                        <tr x-show="open" x-cloak>
                                            <td colspan="7" class="px-4 py-4 bg-gray-800/40 border-t border-gray-700/40">
                                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-8 gap-y-3 text-sm">
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-0.5">ID interno</p>
                                                        <p class="font-mono text-gray-300">{{ $payment->id }}</p>
                                                    </div>
                                                    <div class="col-span-2">
                                                        <p class="text-xs text-gray-500 mb-0.5">AbacatePay ID</p>
                                                        @if($payment->abacatepay_id)
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-mono text-xs text-gray-300 break-all">{{ $payment->abacatepay_id }}</span>
                                                                <button onclick="copyToClipboard('{{ $payment->abacatepay_id }}', this)"
                                                                        class="shrink-0 p-1 rounded text-gray-500 hover:text-cyan-400 hover:bg-gray-700 transition-colors" title="Copiar">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <span class="text-gray-600">—</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-0.5">Criado em</p>
                                                        <p class="text-gray-300">{{ $payment->created_at->format('d/m/Y \à\s H:i:s') }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-0.5">Pago em</p>
                                                        <p class="text-gray-300">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y \à\s H:i:s') : '—' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-0.5">{{ $payment->status === 'pending' ? 'Expira em' : 'Expirou em' }}</p>
                                                        <p class="text-gray-300">{{ $payment->expires_at ? $payment->expires_at->format('d/m/Y \à\s H:i:s') : '—' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                        </table>
                    </div>
                @endif
            </div>

            <!-- Tokens de API -->
            <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-300">Tokens de API</h2>
                    @if($tokens->isNotEmpty())
                        <button
                            onclick="revokeAllTokens({{ $user->id }})"
                            class="text-xs text-red-400 hover:text-red-300 transition-colors"
                        >
                            Revogar todos
                        </button>
                    @endif
                </div>
                @if($tokens->isEmpty())
                    <div class="py-6 text-center text-gray-600 text-sm">Nenhum token cadastrado.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nome</th>
                                    <th class="px-4 py-3 text-left hidden md:table-cell">Criado</th>
                                    <th class="px-4 py-3 text-left hidden md:table-cell">Último uso</th>
                                    <th class="px-4 py-3 text-left">Escopos</th>
                                    <th class="px-4 py-3 w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                @foreach($tokens as $token)
                                    <tr class="text-gray-300" id="token-row-{{ $token->id }}">
                                        <td class="px-4 py-3 font-medium text-sm">{{ $token->name }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 hidden md:table-cell">
                                            {{ $token->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-400 hidden md:table-cell">
                                            {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $abilities = $token->abilities ?? ['*'];
                                            @endphp
                                            @if(in_array('*', $abilities))
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-300">Acesso total</span>
                                            @else
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($abilities as $ability)
                                                        <span class="text-xs px-1.5 py-0.5 rounded bg-gray-700 text-gray-400">{{ $ability }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button
                                                onclick="revokeToken({{ $user->id }}, {{ $token->id }})"
                                                class="text-xs text-red-400 hover:text-red-300 transition-colors px-2 py-1 rounded hover:bg-red-900/20"
                                                title="Revogar token"
                                            >
                                                Revogar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Últimos Lançamentos -->
            <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-300">Últimos Lançamentos</h2>
                </div>
                @if($recentEntries->isEmpty())
                    <div class="py-8 text-center text-gray-600 text-sm">Nenhum lançamento registrado.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3 text-left">Data</th>
                                    <th class="px-4 py-3 text-left">Início</th>
                                    <th class="px-4 py-3 text-left">Fim</th>
                                    <th class="px-4 py-3 text-left">Horas</th>
                                    <th class="px-4 py-3 text-left hidden sm:table-cell">Descrição</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                @foreach($recentEntries as $entry)
                                    @php
                                        $h = floor($entry->hours);
                                        $m = round(($entry->hours - $h) * 60);
                                    @endphp
                                    <tr class="text-gray-300">
                                        <td class="px-4 py-3 text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-xs">{{ $entry->start_time }}</td>
                                        <td class="px-4 py-3 text-xs">{{ $entry->end_time }}</td>
                                        <td class="px-4 py-3 font-mono text-sm">{{ sprintf('%02d:%02d', $h, $m) }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 hidden sm:table-cell max-w-xs truncate">
                                            {{ $entry->description ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Logs AbacatePay -->
    @php
        $allLogs = \App\Models\PaymentLog::where('user_id', $user->id)->latest()->get();
        $logsByPayment = $allLogs->groupBy('payment_id');
    @endphp
    @if($allLogs->isNotEmpty())
    <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-300">Logs AbacatePay</h2>
                <p class="text-xs text-gray-500 mt-0.5">Requisições, respostas e webhooks registrados</p>
            </div>
            <span class="text-xs text-gray-600">{{ $allLogs->count() }} registro(s)</span>
        </div>

        <div class="divide-y divide-gray-700/50">
            @foreach($logsByPayment as $paymentId => $logs)
                @php
                    $relatedPayment = $payments->firstWhere('id', $paymentId);
                @endphp
                <div x-data="{ open: false }">
                    {{-- Cabeçalho do grupo --}}
                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-3 hover:bg-gray-800/30 transition-colors text-left"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-gray-500 shrink-0 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-300">
                                @if($paymentId)
                                    Pagamento #{{ $paymentId }}
                                    @if($relatedPayment)
                                        <span class="ml-2 text-xs text-gray-500 font-normal">
                                            R$ {{ number_format($relatedPayment->amount, 2, ',', '.') }} ·
                                            {{ $relatedPayment->created_at->format('d/m/Y') }}
                                        </span>
                                    @endif
                                @else
                                    Sem pagamento vinculado
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-4">
                            @foreach($logs->groupBy('type') as $type => $typeLogs)
                                @php
                                    $typeStyle = match($type) {
                                        'request' => 'bg-blue-500/20 text-blue-300',
                                        'response' => 'bg-green-500/20 text-green-300',
                                        'webhook'  => 'bg-purple-500/20 text-purple-300',
                                        'error'    => 'bg-red-500/20 text-red-300',
                                        default    => 'bg-gray-500/20 text-gray-400',
                                    };
                                @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $typeStyle }}">
                                    {{ $typeLogs->count() }}x {{ $type }}
                                </span>
                            @endforeach
                        </div>
                    </button>

                    {{-- Lista de logs do grupo --}}
                    <div x-show="open" x-cloak class="bg-gray-950/50 divide-y divide-gray-800/60">
                        @foreach($logs as $log)
                            @php
                                $typeStyle = match($log->type) {
                                    'request'  => ['badge' => 'bg-blue-500/20 text-blue-300',   'dot' => 'bg-blue-400'],
                                    'response' => ['badge' => 'bg-green-500/20 text-green-300', 'dot' => 'bg-green-400'],
                                    'webhook'  => ['badge' => 'bg-purple-500/20 text-purple-300','dot' => 'bg-purple-400'],
                                    'error'    => ['badge' => 'bg-red-500/20 text-red-300',     'dot' => 'bg-red-400'],
                                    default    => ['badge' => 'bg-gray-500/20 text-gray-400',   'dot' => 'bg-gray-400'],
                                };
                                $hasError = $log->type === 'error' || ($log->status_code && $log->status_code >= 400);
                            @endphp
                            <div x-data="{ detailOpen: false }">
                                {{-- Linha do log --}}
                                <button
                                    @click="detailOpen = !detailOpen"
                                    class="w-full flex items-center gap-3 px-5 py-2.5 hover:bg-gray-800/20 transition-colors text-left"
                                >
                                    <span class="w-2 h-2 rounded-full shrink-0 {{ $typeStyle['dot'] }} {{ $hasError ? 'animate-pulse' : '' }}"></span>
                                    <span class="text-xs px-1.5 py-0.5 rounded {{ $typeStyle['badge'] }} shrink-0 uppercase tracking-wide font-medium">{{ $log->type }}</span>

                                    @if($log->endpoint)
                                        <span class="text-xs font-mono text-gray-400 shrink-0">
                                            {{ $log->method ?? 'GET' }} {{ $log->endpoint }}
                                        </span>
                                    @endif

                                    @if($log->status_code)
                                        <span class="text-xs font-mono shrink-0 {{ $log->status_code >= 400 ? 'text-red-400' : 'text-green-400' }}">
                                            HTTP {{ $log->status_code }}
                                        </span>
                                    @endif

                                    @if($log->error_message)
                                        <span class="text-xs text-red-400 truncate flex-1">{{ Str::limit($log->error_message, 60) }}</span>
                                    @endif

                                    @if($log->ip_address)
                                        <span class="text-xs text-gray-600 shrink-0 ml-auto mr-2">{{ $log->ip_address }}</span>
                                    @endif

                                    <span class="text-xs text-gray-600 shrink-0">{{ $log->created_at->format('d/m H:i:s') }}</span>

                                    <svg class="w-3.5 h-3.5 text-gray-600 shrink-0 transition-transform ml-1"
                                         :class="detailOpen ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                {{-- Detalhe JSON --}}
                                <div x-show="detailOpen" x-cloak class="px-5 pb-3">
                                    <div class="grid grid-cols-1 {{ ($log->payload && $log->response) ? 'lg:grid-cols-2' : '' }} gap-3">
                                        @if($log->payload)
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Payload / Request</p>
                                                <div class="relative">
                                                    <pre class="text-xs text-gray-300 bg-gray-900 border border-gray-700 rounded-lg p-3 overflow-x-auto max-h-64 overflow-y-auto leading-relaxed">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    <button
                                                        onclick="copyToClipboard({{ json_encode(json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}, this)"
                                                        class="absolute top-2 right-2 p-1 rounded bg-gray-800 text-gray-500 hover:text-cyan-400 transition-colors"
                                                        title="Copiar JSON"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        @if($log->response)
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Response</p>
                                                <div class="relative">
                                                    <pre class="text-xs text-gray-300 bg-gray-900 border border-gray-700 rounded-lg p-3 overflow-x-auto max-h-64 overflow-y-auto leading-relaxed">{{ json_encode($log->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    <button
                                                        onclick="copyToClipboard({{ json_encode(json_encode($log->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}, this)"
                                                        class="absolute top-2 right-2 p-1 rounded bg-gray-800 text-gray-500 hover:text-cyan-400 transition-colors"
                                                        title="Copiar JSON"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 002 2z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        @if($log->error_message && !$log->payload && !$log->response)
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Erro</p>
                                                <pre class="text-xs text-red-400 bg-gray-900 border border-red-900/40 rounded-lg p-3 whitespace-pre-wrap">{{ $log->error_message }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => { btn.innerHTML = original; }, 1500);
        showToast('Copiado!', TOAST_TYPES.SUCCESS, 1500);
    }).catch(() => {
        showToast('Erro ao copiar.', TOAST_TYPES.ERROR);
    });
}

function toggleAdmin(userId, btn) {
    const isAdmin = btn.dataset.isAdmin === 'true';
    showAdminConfirm(
        isAdmin ? 'Remover permissão de admin deste usuário?' : 'Tornar este usuário administrador?',
        async () => {
            try {
                const res = await fetch(`/admin/users/${userId}/toggle-admin`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.is_admin ? 'Usuário agora é admin.' : 'Admin removido.', TOAST_TYPES.SUCCESS);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast('Erro ao alterar permissão.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao alterar permissão.', TOAST_TYPES.ERROR);
            }
        },
        { title: isAdmin ? 'Remover Admin' : 'Tornar Admin', danger: isAdmin }
    );
}

function activatePremium(userId, months) {
    showAdminConfirm(
        `Ativar/estender ${months} mês(es) de Premium para este usuário?`,
        async () => {
            try {
                const res = await fetch(`/admin/users/${userId}/activate-premium`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ months: parseInt(months) }),
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Premium ativado com sucesso!', TOAST_TYPES.SUCCESS);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast('Erro ao ativar premium.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao ativar premium.', TOAST_TYPES.ERROR);
            }
        },
        { title: 'Ativar Premium', danger: false }
    );
}

function updateStorageQuota(userId, quotaMb) {
    const mb = parseInt(quotaMb);
    if (!mb || mb < 1) { showToast('Informe um valor válido em MB.', TOAST_TYPES.ERROR); return; }
    showAdminConfirm(
        `Definir cota de armazenamento para ${mb} MB?`,
        async () => {
            try {
                const res = await fetch(`/admin/users/${userId}/storage-quota`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ quota_mb: mb }),
                });
                const data = await res.json();
                if (data.success) {
                    showToast(`Cota atualizada para ${mb} MB.`, TOAST_TYPES.SUCCESS);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast('Erro ao atualizar cota.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao atualizar cota.', TOAST_TYPES.ERROR);
            }
        },
        { title: 'Atualizar Cota', danger: false }
    );
}

function revokeToken(userId, tokenId) {
    showAdminConfirm(
        'Revogar este token de API? O usuário perderá acesso imediatamente.',
        async () => {
            try {
                const res = await fetch(`/admin/users/${userId}/tokens/${tokenId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Token revogado.', TOAST_TYPES.SUCCESS);
                    const row = document.getElementById(`token-row-${tokenId}`);
                    if (row) row.remove();
                } else {
                    showToast('Erro ao revogar token.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao revogar token.', TOAST_TYPES.ERROR);
            }
        },
        { title: 'Revogar Token' }
    );
}

function revokeAllTokens(userId) {
    showAdminConfirm(
        'Revogar TODOS os tokens deste usuário? Isso encerrará todas as integrações ativas.',
        async () => {
            try {
                const res = await fetch(`/admin/users/${userId}/tokens`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Todos os tokens revogados.', TOAST_TYPES.SUCCESS);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast('Erro ao revogar tokens.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao revogar tokens.', TOAST_TYPES.ERROR);
            }
        },
        { title: 'Revogar Todos os Tokens' }
    );
}

function disable2fa(userId) {
    showAdminConfirm(
        'Desativar o 2FA deste usuário? Ele poderá fazer login sem o código de verificação.',
        async () => {
            try {
                const res = await fetch(`/admin/users/${userId}/disable-2fa`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                });
                const data = await res.json();
                if (data.success) {
                    showToast('2FA desativado com sucesso.', TOAST_TYPES.SUCCESS);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast('Erro ao desativar 2FA.', TOAST_TYPES.ERROR);
                }
            } catch (e) {
                showToast('Erro ao desativar 2FA.', TOAST_TYPES.ERROR);
            }
        },
        { title: 'Desativar 2FA' }
    );
}
</script>
@endpush
@endsection
