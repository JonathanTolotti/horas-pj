<?php

use App\Models\Project;
use App\Models\Setting;
use App\Models\TimeEntry;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Setting::create([
        'user_id' => $this->user->id,
        'hourly_rate' => 100,
        'extra_value' => 0,
        'discount_value' => 0,
    ]);
});

describe('TimeEntry CRUD', function () {
    test('usuario pode criar lancamento de horas', function () {
        $response = $this->actingAs($this->user)->postJson('/time-entries', [
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Desenvolvimento de feature',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $entry = \App\Models\TimeEntry::where('description', 'Desenvolvimento de feature')->first();
        expect($entry)->not->toBeNull();
        expect($entry->user_id)->toBe($this->user->id);
        expect($entry->date->format('Y-m-d'))->toBe('2024-02-15');
        expect($entry->start_time)->toBe('09:00');
        expect($entry->end_time)->toBe('12:00');
        expect((float) $entry->hours)->toBe(3.0);
    });

    test('usuario pode criar lancamento com projeto', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Teste',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson('/time-entries', [
            'date' => '2024-02-15',
            'start_time' => '14:00',
            'end_time' => '18:00',
            'description' => 'Trabalho no projeto',
            'project_id' => $project->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('time_entries', [
            'user_id' => $this->user->id,
            'project_id' => $project->id,
            'hours' => 4.0,
        ]);
    });

    test('usuario pode excluir lancamento', function () {
        $entry = TimeEntry::create([
            'user_id' => $this->user->id,
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'hours' => 3.0,
            'description' => 'Teste',
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/time-entries/{$entry->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('time_entries', ['id' => $entry->id]);
    });

    test('usuario nao pode excluir lancamento de outro usuario', function () {
        $otherUser = User::factory()->create();
        $entry = TimeEntry::create([
            'user_id' => $otherUser->id,
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'hours' => 3.0,
            'description' => 'Teste',
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/time-entries/{$entry->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('time_entries', ['id' => $entry->id]);
    });
});

describe('TimeEntry Validation', function () {
    test('data e obrigatoria', function () {
        $response = $this->actingAs($this->user)->postJson('/time-entries', [
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Teste',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    });

    test('hora fim deve ser posterior a hora inicio', function () {
        $response = $this->actingAs($this->user)->postJson('/time-entries', [
            'date' => '2024-02-15',
            'start_time' => '12:00',
            'end_time' => '09:00',
            'description' => 'Teste',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_time']);
    });

    test('descricao e obrigatoria', function () {
        $response = $this->actingAs($this->user)->postJson('/time-entries', [
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    });

    test('nao permite lancamento sobreposto', function () {
        // Criar primeiro lançamento via API para garantir consistência
        $first = $this->actingAs($this->user)->postJson('/time-entries', [
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Primeiro lancamento',
        ]);
        $first->assertStatus(200);

        // Verificar que o primeiro lançamento foi criado
        $entry = TimeEntry::where('description', 'Primeiro lancamento')->first();
        expect($entry)->not->toBeNull();

        // Tentar criar lançamento sobreposto (mesmo horário exato)
        $response = $this->actingAs($this->user)->postJson('/time-entries', [
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Lancamento sobreposto',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    });
});

describe('TimeEntry Stats', function () {
    test('retorna estatisticas do mes', function () {
        TimeEntry::create([
            'user_id' => $this->user->id,
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'hours' => 3.0,
            'description' => 'Teste',
            'month_reference' => '2024-02',
        ]);

        TimeEntry::create([
            'user_id' => $this->user->id,
            'date' => '2024-02-16',
            'start_time' => '14:00',
            'end_time' => '18:00',
            'hours' => 4.0,
            'description' => 'Teste 2',
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->getJson('/time-entries/stats?month=2024-02');

        $response->assertStatus(200)
            ->assertJsonFragment(['total_hours' => 7.0]);
    });
});

describe('Dashboard Access', function () {
    test('usuario autenticado pode acessar dashboard', function () {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
    });

    test('usuario nao autenticado e redirecionado para login', function () {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    });
});
