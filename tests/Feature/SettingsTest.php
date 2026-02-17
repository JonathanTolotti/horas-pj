<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\TimeEntry;
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

describe('Settings', function () {
    test('usuario pode acessar pagina de configuracoes', function () {
        $response = $this->actingAs($this->user)->get('/settings');

        $response->assertStatus(200);
    });

    test('usuario pode atualizar taxa horaria', function () {
        $response = $this->actingAs($this->user)->putJson('/settings', [
            'hourly_rate' => 150,
            'extra_value' => 50,
            'discount_value' => 25,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->settings->refresh();
        expect($this->settings->hourly_rate)->toBe('150.00');
        expect($this->settings->extra_value)->toBe('50.00');
        expect($this->settings->discount_value)->toBe('25.00');
    });

    test('taxa horaria deve ser positiva', function () {
        $response = $this->actingAs($this->user)->putJson('/settings', [
            'hourly_rate' => -10,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hourly_rate']);
    });

    test('usuario pode atualizar auto_save_tracking', function () {
        $response = $this->actingAs($this->user)->putJson('/settings', [
            'hourly_rate' => 100,
            'extra_value' => 0,
            'discount_value' => 0,
            'auto_save_tracking' => true,
        ]);

        $response->assertStatus(200);

        $this->settings->refresh();
        expect((bool) $this->settings->auto_save_tracking)->toBeTrue();
    });
});

describe('Projects CRUD', function () {
    test('usuario pode criar projeto', function () {
        // Usuario premium para criar projetos ilimitados
        Subscription::create([
            'user_id' => $this->user->id,
            'plan' => 'premium',
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
        ]);

        $response = $this->actingAs($this->user)->postJson('/projects', [
            'name' => 'Projeto Alpha',
            'active' => true,
            'is_default' => false,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('projects', [
            'user_id' => $this->user->id,
            'name' => 'Projeto Alpha',
        ]);
    });

    test('usuario pode atualizar projeto', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Original',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->putJson("/projects/{$project->id}", [
            'name' => 'Projeto Atualizado',
            'active' => false,
            'is_default' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $project->refresh();
        expect($project->name)->toBe('Projeto Atualizado');
        expect($project->active)->toBeFalse();
        expect($project->is_default)->toBeTrue();
    });

    test('usuario pode excluir projeto sem lancamentos', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Teste',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    });

    test('usuario nao pode excluir projeto com lancamentos', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto Com Lancamentos',
            'active' => true,
        ]);

        TimeEntry::create([
            'user_id' => $this->user->id,
            'project_id' => $project->id,
            'date' => '2024-02-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'hours' => 3,
            'description' => 'Trabalho',
            'month_reference' => '2024-02',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/projects/{$project->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    });

    test('usuario nao pode modificar projeto de outro usuario', function () {
        $otherUser = User::factory()->create();
        $project = Project::create([
            'user_id' => $otherUser->id,
            'name' => 'Projeto do Outro',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->putJson("/projects/{$project->id}", [
            'name' => 'Tentativa de Modificar',
        ]);

        $response->assertStatus(403);
    });

    test('definir projeto como padrao remove padrao dos outros', function () {
        $project1 = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto 1',
            'active' => true,
            'is_default' => true,
        ]);

        $project2 = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto 2',
            'active' => true,
            'is_default' => false,
        ]);

        $this->actingAs($this->user)->putJson("/projects/{$project2->id}", [
            'name' => 'Projeto 2',
            'active' => true,
            'is_default' => true,
        ]);

        $project1->refresh();
        $project2->refresh();

        expect($project1->is_default)->toBeFalse();
        expect($project2->is_default)->toBeTrue();
    });
});

describe('Companies CRUD', function () {
    test('usuario pode criar empresa', function () {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan' => 'premium',
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
        ]);

        $response = $this->actingAs($this->user)->postJson('/companies', [
            'name' => 'Empresa Alpha',
            'cnpj' => '12.345.678/0001-90',
            'active' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('companies', [
            'user_id' => $this->user->id,
            'name' => 'Empresa Alpha',
        ]);
    });

    test('usuario pode atualizar empresa', function () {
        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa Original',
            'cnpj' => '12.345.678/0001-90',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->putJson("/companies/{$company->id}", [
            'name' => 'Empresa Atualizada',
            'cnpj' => '98.765.432/0001-21',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $company->refresh();
        expect($company->name)->toBe('Empresa Atualizada');
    });

    test('usuario pode excluir empresa sem vinculos', function () {
        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa Teste',
            'cnpj' => '11.111.111/0001-11',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    });

    test('usuario nao pode excluir empresa com vinculos a projetos', function () {
        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa Com Vinculos',
            'cnpj' => '22.222.222/0001-22',
            'active' => true,
        ]);

        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto',
            'active' => true,
        ]);

        $project->companies()->attach($company->id, ['percentage' => 50]);

        $response = $this->actingAs($this->user)->deleteJson("/companies/{$company->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    });

    test('usuario nao pode modificar empresa de outro usuario', function () {
        $otherUser = User::factory()->create();
        $company = Company::create([
            'user_id' => $otherUser->id,
            'name' => 'Empresa do Outro',
            'cnpj' => '33.333.333/0001-33',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->putJson("/companies/{$company->id}", [
            'name' => 'Tentativa de Modificar',
            'cnpj' => '99.999.999/0001-99',
        ]);

        $response->assertStatus(403);
    });
});

describe('Company-Project Association', function () {
    test('usuario pode vincular empresa a projeto', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto',
            'active' => true,
        ]);

        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa',
            'cnpj' => '44.444.444/0001-44',
            'active' => true,
        ]);

        $response = $this->actingAs($this->user)->postJson("/projects/{$project->id}/companies", [
            'company_id' => $company->id,
            'percentage' => 60,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('company_project', [
            'project_id' => $project->id,
            'company_id' => $company->id,
            'percentage' => 60,
        ]);
    });

    test('soma das porcentagens nao pode ultrapassar 100', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto',
            'active' => true,
        ]);

        $company1 = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa 1',
            'cnpj' => '55.555.555/0001-55',
            'active' => true,
        ]);

        $company2 = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa 2',
            'cnpj' => '66.666.666/0001-66',
            'active' => true,
        ]);

        $project->companies()->attach($company1->id, ['percentage' => 70]);

        $response = $this->actingAs($this->user)->postJson("/projects/{$project->id}/companies", [
            'company_id' => $company2->id,
            'percentage' => 50,
        ]);

        $response->assertStatus(422);
    });

    test('usuario pode atualizar porcentagem de empresa', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto',
            'active' => true,
        ]);

        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa',
            'cnpj' => '77.777.777/0001-77',
            'active' => true,
        ]);

        $project->companies()->attach($company->id, ['percentage' => 50]);

        $response = $this->actingAs($this->user)->putJson("/projects/{$project->id}/companies/{$company->id}", [
            'percentage' => 75,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('company_project', [
            'project_id' => $project->id,
            'company_id' => $company->id,
            'percentage' => 75,
        ]);
    });

    test('usuario pode desvincular empresa de projeto', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto',
            'active' => true,
        ]);

        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa',
            'cnpj' => '88.888.888/0001-88',
            'active' => true,
        ]);

        $project->companies()->attach($company->id, ['percentage' => 50]);

        $response = $this->actingAs($this->user)->deleteJson("/projects/{$project->id}/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('company_project', [
            'project_id' => $project->id,
            'company_id' => $company->id,
        ]);
    });

    test('nao pode vincular mesma empresa duas vezes', function () {
        $project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto',
            'active' => true,
        ]);

        $company = Company::create([
            'user_id' => $this->user->id,
            'name' => 'Empresa',
            'cnpj' => '99.999.999/0001-99',
            'active' => true,
        ]);

        $project->companies()->attach($company->id, ['percentage' => 50]);

        $response = $this->actingAs($this->user)->postJson("/projects/{$project->id}/companies", [
            'company_id' => $company->id,
            'percentage' => 30,
        ]);

        $response->assertStatus(422);
    });
});

describe('Premium Limits', function () {
    test('usuario free tem limite de projetos', function () {
        // Usuario free (sem subscription)
        // Criar o primeiro projeto permitido
        Project::create([
            'user_id' => $this->user->id,
            'name' => 'Projeto 1',
            'active' => true,
        ]);

        // Tentar criar segundo projeto (deve falhar para free)
        $response = $this->actingAs($this->user)->postJson('/projects', [
            'name' => 'Projeto 2',
            'active' => true,
        ]);

        $response->assertStatus(403)
            ->assertJson(['premium_required' => true]);
    });

    test('usuario premium nao tem limite de projetos', function () {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan' => 'premium',
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
        ]);

        // Criar varios projetos
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->actingAs($this->user)->postJson('/projects', [
                'name' => "Projeto $i",
                'active' => true,
            ]);

            $response->assertStatus(200);
        }

        expect(Project::forUser($this->user->id)->count())->toBe(5);
    });
});
