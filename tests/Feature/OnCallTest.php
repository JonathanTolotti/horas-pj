<?php

use App\Models\OnCallPeriod;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\TimeEntry;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Setting::create([
        'user_id' => $this->user->id,
        'hourly_rate' => 100,
        'extra_value' => 0,
        'discount_value' => 0,
        'on_call_hourly_rate' => 33.33,
    ]);

    // Criar assinatura premium para permitir uso do sobreaviso
    Subscription::create([
        'user_id' => $this->user->id,
        'plan' => 'premium',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addYear(),
    ]);
});

describe('OnCall CRUD', function () {
    test('usuario premium pode criar periodo de sobreaviso', function () {
        $response = $this->actingAs($this->user)->postJson('/on-call', [
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-17 18:00',
            'hourly_rate' => 33.33,
            'description' => 'Sobreaviso fim de semana',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('on_call_periods', [
            'user_id' => $this->user->id,
            'total_hours' => 48.0,
            'description' => 'Sobreaviso fim de semana',
        ]);
    });

    test('usuario premium pode criar sobreaviso com projeto', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Teste',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/on-call', [
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-16 18:00',
            'project_id' => $project->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('on_call_periods', [
            'user_id' => $this->user->id,
            'project_id' => $project->id,
        ]);
    });

    test('usuario pode atualizar periodo de sobreaviso', function () {
        $period = OnCallPeriod::create([
            'user_id' => $this->user->id,
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-16 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 24,
            'worked_hours' => 0,
            'on_call_hours' => 24,
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->putJson("/on-call/{$period->id}", [
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-17 18:00',
            'hourly_rate' => 40,
            'description' => 'Atualizado',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $period->refresh();
        expect($period->total_hours)->toBe('48.00');
        expect($period->hourly_rate)->toBe('40.00');
        expect($period->description)->toBe('Atualizado');
    });

    test('usuario pode excluir periodo de sobreaviso', function () {
        $period = OnCallPeriod::create([
            'user_id' => $this->user->id,
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-16 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 24,
            'worked_hours' => 0,
            'on_call_hours' => 24,
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/on-call/{$period->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('on_call_periods', ['id' => $period->id]);
    });

    test('usuario nao pode excluir sobreaviso de outro usuario', function () {
        $otherUser = User::factory()->create();
        $period = OnCallPeriod::create([
            'user_id' => $otherUser->id,
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-16 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 24,
            'worked_hours' => 0,
            'on_call_hours' => 24,
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/on-call/{$period->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('on_call_periods', ['id' => $period->id]);
    });
});

describe('OnCall Premium', function () {
    test('usuario free nao pode criar sobreaviso', function () {
        $freeUser = User::factory()->create();
        Setting::create([
            'user_id' => $freeUser->id,
            'hourly_rate' => 100,
            'extra_value' => 0,
            'discount_value' => 0,
        ]);

        $response = $this->actingAs($freeUser)->postJson('/on-call', [
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-16 18:00',
        ]);

        $response->assertStatus(403);
    });

    test('usuario free pode listar sobreavisos', function () {
        $freeUser = User::factory()->create();
        Setting::create([
            'user_id' => $freeUser->id,
            'hourly_rate' => 100,
            'extra_value' => 0,
            'discount_value' => 0,
        ]);

        $response = $this->actingAs($freeUser)->getJson('/on-call?month=2024-02');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });
});

describe('OnCall Validation', function () {
    test('data fim deve ser posterior a data inicio', function () {
        $response = $this->actingAs($this->user)->postJson('/on-call', [
            'start_datetime' => '2024-02-17 18:00',
            'end_datetime' => '2024-02-15 18:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_datetime']);
    });

    test('data inicio e obrigatoria', function () {
        $response = $this->actingAs($this->user)->postJson('/on-call', [
            'end_datetime' => '2024-02-17 18:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_datetime']);
    });

    test('data fim e obrigatoria', function () {
        $response = $this->actingAs($this->user)->postJson('/on-call', [
            'start_datetime' => '2024-02-15 18:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_datetime']);
    });
});

describe('OnCall Time Entry Linking', function () {
    test('lancamento dentro do periodo e vinculado automaticamente', function () {
        // Criar período de sobreaviso primeiro
        $period = OnCallPeriod::create([
            'user_id' => $this->user->id,
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-17 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 48,
            'worked_hours' => 0,
            'on_call_hours' => 48,
            'month_reference' => '2024-02',
        ]);

        // Criar lançamento dentro do período
        $response = $this->actingAs($this->user)->postJson('/time-entries', [
            'date' => '2024-02-16',
            'start_time' => '10:00',
            'end_time' => '14:00',
            'description' => 'Trabalho durante sobreaviso',
        ]);

        $response->assertStatus(200);

        // Verificar que o lançamento foi vinculado ao período
        $entry = TimeEntry::where('description', 'Trabalho durante sobreaviso')->first();
        expect($entry->on_call_period_id)->toBe($period->id);

        // Verificar que as horas foram recalculadas
        $period->refresh();
        expect($period->worked_hours)->toBe('4.00');
        expect($period->on_call_hours)->toBe('44.00');
    });

    test('excluir periodo desvincula lancamentos', function () {
        $period = OnCallPeriod::create([
            'user_id' => $this->user->id,
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-17 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 48,
            'worked_hours' => 4,
            'on_call_hours' => 44,
            'month_reference' => '2024-02',
        ]);

        $entry = TimeEntry::create([
            'user_id' => $this->user->id,
            'date' => '2024-02-16',
            'start_time' => '10:00',
            'end_time' => '14:00',
            'hours' => 4,
            'description' => 'Trabalho',
            'month_reference' => '2024-02',
            'on_call_period_id' => $period->id,
        ]);

        $this->actingAs($this->user)->deleteJson("/on-call/{$period->id}");

        $entry->refresh();
        expect($entry->on_call_period_id)->toBeNull();
    });
});

describe('OnCall Stats', function () {
    test('retorna estatisticas de sobreaviso do mes', function () {
        OnCallPeriod::create([
            'user_id' => $this->user->id,
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-17 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 48,
            'worked_hours' => 8,
            'on_call_hours' => 40,
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->getJson('/on-call/stats?month=2024-02');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'stats' => [
                    'total_on_call_hours',
                    'total_on_call_revenue',
                    'periods_count',
                ],
            ]);
    });

    test('lista periodos do mes corretamente', function () {
        OnCallPeriod::create([
            'user_id' => $this->user->id,
            'start_datetime' => '2024-02-15 18:00',
            'end_datetime' => '2024-02-17 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 48,
            'worked_hours' => 0,
            'on_call_hours' => 48,
            'month_reference' => '2024-02',
        ]);

        OnCallPeriod::create([
            'user_id' => $this->user->id,
            'start_datetime' => '2024-03-01 18:00',
            'end_datetime' => '2024-03-03 18:00',
            'hourly_rate' => 33.33,
            'total_hours' => 48,
            'worked_hours' => 0,
            'on_call_hours' => 48,
            'month_reference' => '2024-03',
        ]);

        $response = $this->actingAs($this->user)->getJson('/on-call?month=2024-02');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $periods = $response->json('periods');
        expect(count($periods))->toBe(1);
    });
});
