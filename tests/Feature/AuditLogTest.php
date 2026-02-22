<?php

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->settings = Setting::create([
        'user_id' => $this->user->id,
        'hourly_rate' => 100,
        'extra_value' => 0,
        'discount_value' => 0,
    ]);
});

describe('Auditoria de Configurações', function () {
    test('atualizar settings gera log com old e new values', function () {
        $this->actingAs($this->user)->putJson('/settings', [
            'hourly_rate' => 200,
            'extra_value' => 50,
            'discount_value' => 10,
        ]);

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'setting')
            ->where('action', 'updated')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->entity_label)->toBe('Configurações Gerais');
        expect($log->new_values)->toHaveKey('hourly_rate');
        expect($log->old_values)->toHaveKey('hourly_rate');
    });

    test('campos internos nao sao incluidos no log de settings', function () {
        $this->actingAs($this->user)->putJson('/settings', [
            'hourly_rate' => 150,
            'extra_value' => 0,
            'discount_value' => 0,
        ]);

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'setting')
            ->where('action', 'updated')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->new_values)->not->toHaveKey('id');
        expect($log->new_values)->not->toHaveKey('user_id');
        expect($log->new_values)->not->toHaveKey('created_at');
        expect($log->new_values)->not->toHaveKey('updated_at');
    });

    test('atualizar settings sem mudancas nao gera log', function () {
        // Usar valores idênticos aos atuais
        $this->settings->update(['hourly_rate' => 100]);

        // Disparar novamente com mesmo valor (sem mudança real)
        $this->settings->hourly_rate = 100;
        $this->settings->save();

        $count = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'setting')
            ->count();

        expect($count)->toBe(0);
    });
});

describe('Auditoria de Projetos', function () {
    test('criar projeto gera log created', function () {
        $this->actingAs($this->user)->postJson('/projects', [
            'name' => 'Projeto Auditado',
            'active' => true,
            'is_default' => false,
        ]);

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'project')
            ->where('action', 'created')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->entity_label)->toBe('Projeto Auditado');
        expect($log->new_values)->toHaveKey('name');
        expect($log->old_values)->toBeNull();
    });

    test('atualizar projeto gera log updated com diff', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Nome Original',
            'active' => true,
        ]);

        $this->actingAs($this->user)->putJson("/projects/{$project->id}", [
            'name' => 'Nome Novo',
            'active' => false,
            'is_default' => false,
        ]);

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'project')
            ->where('action', 'updated')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->new_values)->toHaveKey('name');
        expect($log->old_values['name'])->toBe('Nome Original');
        expect($log->new_values['name'])->toBe('Nome Novo');
    });

    test('excluir projeto gera log deleted', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Para Excluir',
            'active' => true,
        ]);

        $this->actingAs($this->user)->deleteJson("/projects/{$project->id}");

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'project')
            ->where('action', 'deleted')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->entity_label)->toBe('Projeto Para Excluir');
        expect($log->old_values)->toBeNull();
        expect($log->new_values)->toBeNull();
    });
});

describe('Auditoria de Empresas', function () {
    test('criar empresa gera log created', function () {
        $this->actingAs($this->user)->postJson('/companies', [
            'name' => 'Empresa Auditada',
            'cnpj' => '11.222.333/0001-44',
            'active' => true,
        ]);

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'company')
            ->where('action', 'created')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->entity_label)->toBe('Empresa Auditada');
        expect($log->new_values)->toHaveKey('name');
        expect($log->old_values)->toBeNull();
    });

    test('atualizar empresa gera log updated com diff', function () {
        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa Original',
            'cnpj' => '11.222.333/0001-44',
            'active' => true,
        ]);

        $this->actingAs($this->user)->putJson("/companies/{$company->id}", [
            'name' => 'Empresa Renomeada',
            'cnpj' => '11.222.333/0001-44',
            'active' => true,
        ]);

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'company')
            ->where('action', 'updated')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->old_values['name'])->toBe('Empresa Original');
        expect($log->new_values['name'])->toBe('Empresa Renomeada');
    });
});

describe('Auditoria de Vínculos Empresa-Projeto', function () {
    test('vincular empresa a projeto gera log created', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Vinculo',
            'active' => true,
        ]);

        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa Vinculo',
            'cnpj' => '11.222.333/0001-44',
            'active' => true,
        ]);

        $this->actingAs($this->user)->postJson("/projects/{$project->id}/companies", [
            'company_id' => $company->id,
            'percentage' => 100,
        ]);

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'company_project')
            ->where('action', 'created')
            ->first();

        expect($log)->not->toBeNull();
        expect($log->entity_label)->toBe("Projeto Vinculo ← Empresa Vinculo");
        expect((float) $log->new_values['percentage'])->toBe(100.0);
    });

    test('desvincular empresa de projeto gera log deleted', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Desvinculo',
            'active' => true,
        ]);

        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa Desvinculo',
            'cnpj' => '55.666.777/0001-88',
            'active' => true,
        ]);

        $project->companies()->attach($company->id, ['percentage' => 60]);

        $this->actingAs($this->user)->deleteJson("/projects/{$project->id}/companies/{$company->id}");

        $log = AuditLog::forUser($this->user->id)
            ->where('entity_type', 'company_project')
            ->where('action', 'deleted')
            ->first();

        expect($log)->not->toBeNull();
        expect((float) $log->old_values['percentage'])->toBe(60.0);
    });
});

describe('Exibição na View de Settings', function () {
    test('pagina settings exibe secao historico de alteracoes', function () {
        $response = $this->actingAs($this->user)->get('/settings');

        $response->assertStatus(200);
        $response->assertSee('Histórico de Alterações');
    });

    test('pagina settings exibe logs registrados', function () {
        AuditLog::record(
            userId: $this->user->id,
            entityType: 'project',
            entityId: 1,
            entityLabel: 'Projeto Visivel',
            action: 'created',
            oldValues: null,
            newValues: ['name' => 'Projeto Visivel'],
        );

        $response = $this->actingAs($this->user)->get('/settings');

        $response->assertStatus(200);
        $response->assertSee('Projeto Visivel');
    });

    test('logs de outro usuario nao aparecem na view', function () {
        $otherUser = User::factory()->create();

        AuditLog::record(
            userId: $otherUser->id,
            entityType: 'project',
            entityId: 99,
            entityLabel: 'Projeto Secreto',
            action: 'created',
            oldValues: null,
            newValues: ['name' => 'Projeto Secreto'],
        );

        $response = $this->actingAs($this->user)->get('/settings');

        $response->assertStatus(200);
        $response->assertDontSee('Projeto Secreto');
    });
});

describe('Endpoint de Filtro AJAX /settings/audit-logs', function () {
    test('retorna apenas logs do tipo solicitado', function () {
        AuditLog::record(
            userId: $this->user->id,
            entityType: 'project',
            entityId: 1,
            entityLabel: 'Projeto Filtrado',
            action: 'created',
            oldValues: null,
            newValues: ['name' => 'Projeto Filtrado'],
        );
        AuditLog::record(
            userId: $this->user->id,
            entityType: 'company',
            entityId: 2,
            entityLabel: 'Empresa Nao Deve Aparecer',
            action: 'created',
            oldValues: null,
            newValues: ['name' => 'Empresa Nao Deve Aparecer'],
        );

        $response = $this->actingAs($this->user)->get('/settings/audit-logs?filter=project');

        $response->assertStatus(200);
        $response->assertSee('Projeto Filtrado');
        $response->assertDontSee('Empresa Nao Deve Aparecer');
    });

    test('filtro invalido e ignorado e retorna todos', function () {
        AuditLog::record(
            userId: $this->user->id,
            entityType: 'project',
            entityId: 1,
            entityLabel: 'Projeto Qualquer',
            action: 'created',
            oldValues: null,
            newValues: ['name' => 'Projeto Qualquer'],
        );

        $response = $this->actingAs($this->user)->get('/settings/audit-logs?filter=invalido');

        $response->assertStatus(200);
        $response->assertSee('Projeto Qualquer');
    });

    test('retorna os 30 ultimos do tipo selecionado', function () {
        for ($i = 1; $i <= 35; $i++) {
            AuditLog::record(
                userId: $this->user->id,
                entityType: 'project',
                entityId: $i,
                entityLabel: "Projeto {$i}",
                action: 'created',
                oldValues: null,
                newValues: ['name' => "Projeto {$i}"],
            );
        }
        for ($i = 1; $i <= 5; $i++) {
            AuditLog::record(
                userId: $this->user->id,
                entityType: 'company',
                entityId: $i,
                entityLabel: "Empresa {$i}",
                action: 'created',
                oldValues: null,
                newValues: ['name' => "Empresa {$i}"],
            );
        }

        $response = $this->actingAs($this->user)->get('/settings/audit-logs?filter=project');

        $response->assertStatus(200);
        $logs = $response->viewData('auditLogs');
        expect($logs)->toHaveCount(30);
        expect($logs->every(fn($l) => $l->entity_type === 'project'))->toBeTrue();
    });

    test('nao e acessivel sem autenticacao', function () {
        $response = $this->get('/settings/audit-logs');
        $response->assertRedirect('/login');
    });

    test('nao retorna logs de outro usuario', function () {
        $otherUser = User::factory()->create();
        AuditLog::record(
            userId: $otherUser->id,
            entityType: 'project',
            entityId: 1,
            entityLabel: 'Projeto Alheio',
            action: 'created',
            oldValues: null,
            newValues: ['name' => 'Projeto Alheio'],
        );

        $response = $this->actingAs($this->user)->get('/settings/audit-logs?filter=project');

        $response->assertStatus(200);
        $response->assertDontSee('Projeto Alheio');
    });
});
