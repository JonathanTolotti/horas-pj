@php
    $entityColors = [
        'setting'         => 'bg-emerald-500/20 text-emerald-300',
        'project'         => 'bg-cyan-500/20 text-cyan-300',
        'company'         => 'bg-blue-500/20 text-blue-300',
        'company_project' => 'bg-purple-500/20 text-purple-300',
    ];
    $actionColors = [
        'created' => 'bg-green-500/20 text-green-300',
        'updated' => 'bg-yellow-500/20 text-yellow-300',
        'deleted' => 'bg-red-500/20 text-red-300',
    ];
@endphp

@forelse($auditLogs as $log)
    @php
        $entityColor = $entityColors[$log->entity_type] ?? 'bg-gray-500/20 text-gray-300';
        $actionColor = $actionColors[$log->action] ?? 'bg-gray-500/20 text-gray-300';
    @endphp
    <div class="flex flex-col sm:flex-row sm:items-start gap-2 p-3 bg-gray-800/50 rounded-lg border border-gray-700/50">
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs text-gray-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $entityColor }}">
                {{ \App\Models\AuditLog::entityTypeLabel($log->entity_type) }}
            </span>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $actionColor }}">
                {{ \App\Models\AuditLog::actionLabel($log->action) }}
            </span>
        </div>
        <div class="flex-1 min-w-0">
            @if($log->entity_label)
                <span class="text-sm text-white font-medium">{{ $log->entity_label }}</span>
            @endif
            @if($log->action === 'updated' && $log->old_values && $log->new_values)
                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1">
                    @foreach($log->new_values as $field => $newVal)
                        @php
                            $oldVal = $log->old_values[$field] ?? null;
                            $label = \App\Models\AuditLog::FIELD_LABELS[$field] ?? $field;
                        @endphp
                        <span class="text-xs text-gray-400">
                            <span class="text-gray-500">{{ $label }}:</span>
                            <span class="line-through text-gray-600">{{ \App\Models\AuditLog::formatFieldValue($field, $oldVal) }}</span>
                            <span class="text-gray-300">→ {{ \App\Models\AuditLog::formatFieldValue($field, $newVal) }}</span>
                        </span>
                    @endforeach
                </div>
            @elseif($log->action === 'created' && $log->new_values)
                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1">
                    @foreach($log->new_values as $field => $val)
                        @php $label = \App\Models\AuditLog::FIELD_LABELS[$field] ?? $field; @endphp
                        <span class="text-xs text-gray-400">
                            <span class="text-gray-500">{{ $label }}:</span>
                            <span class="text-gray-300">{{ \App\Models\AuditLog::formatFieldValue($field, $val) }}</span>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@empty
    <p class="text-center py-8 text-gray-500">Nenhuma alteração registrada nesta categoria.</p>
@endforelse
