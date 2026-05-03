@php
    $storageService = app(\App\Services\StorageService::class);
    $quota = $storageService->getQuotaData(auth()->user());
@endphp

<div class="storage-quota-widget">
    <div class="flex items-center justify-between text-xs mb-1.5">
        <span class="text-gray-400 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
            </svg>
            Armazenamento
        </span>
        <span class="{{ $quota['text_color'] }} font-medium">
            {{ $quota['used_mb'] }} MB / {{ $quota['quota_mb'] }} MB
        </span>
    </div>
    <div class="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
        <div class="{{ $quota['color_class'] }} h-1.5 rounded-full transition-all duration-300"
             style="width: {{ $quota['percentage'] }}%"></div>
    </div>
    @if($quota['full'])
        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Limite atingido. Exclua arquivos para liberar espaço.
        </p>
    @elseif($quota['near_full'])
        <p class="text-yellow-400 text-xs mt-1.5 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            Limite quase atingido ({{ $quota['percentage'] }}%).
        </p>
    @endif
</div>
