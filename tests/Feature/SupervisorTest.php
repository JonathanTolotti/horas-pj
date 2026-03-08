<?php

use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SupervisorAccess;
use App\Models\SupervisorInvitation;
use App\Models\User;

function makePremiumUser(array $attrs = []): User
{
    $user = User::factory()->create($attrs);
    Setting::create([
        'user_id' => $user->id,
        'hourly_rate' => 100,
        'extra_value' => 0,
        'discount_value' => 0,
    ]);
    Subscription::create([
        'user_id' => $user->id,
        'plan' => 'premium',
        'status' => 'active',
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addYear(),
    ]);
    return $user;
}

beforeEach(function () {
    $this->owner = makePremiumUser();
    $this->supervisor = makePremiumUser();
});

describe('Convite de Supervisor', function () {
    test('usuario premium pode convidar supervisor por email', function () {
        $response = $this->actingAs($this->owner)->postJson('/settings/supervisors/invite', [
            'email' => $this->supervisor->email,
            'can_view_financials' => true,
            'can_view_analytics' => false,
            'can_export' => false,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('supervisor_invitations', [
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'can_view_financials' => true,
            'status' => 'pending',
        ]);
    });

    test('usuario nao pode convidar email inexistente no sistema', function () {
        $response = $this->actingAs($this->owner)->postJson('/settings/supervisors/invite', [
            'email' => 'naoexiste@teste.com',
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);
        $this->assertDatabaseMissing('supervisor_invitations', ['user_id' => $this->owner->id]);
    });

    test('usuario nao pode se auto-convidar como supervisor', function () {
        $response = $this->actingAs($this->owner)->postJson('/settings/supervisors/invite', [
            'email' => $this->owner->email,
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);
    });

    test('usuario nao premium nao pode convidar supervisor', function () {
        $freeUser = User::factory()->create();
        Setting::create(['user_id' => $freeUser->id, 'hourly_rate' => 100, 'extra_value' => 0, 'discount_value' => 0]);

        $response = $this->actingAs($freeUser)->postJson('/settings/supervisors/invite', [
            'email' => $this->supervisor->email,
        ]);

        $response->assertStatus(403);
    });

    test('convite com data de expiracao no passado e rejeitado', function () {
        $response = $this->actingAs($this->owner)->postJson('/settings/supervisors/invite', [
            'email' => $this->supervisor->email,
            'expires_at' => now()->subDay()->toDateTimeString(),
        ]);

        $response->assertStatus(422);
    });

    test('usuario nao pode convidar supervisor que ja tem acesso', function () {
        SupervisorAccess::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'can_view_financials' => false,
            'can_view_analytics' => false,
            'can_export' => false,
        ]);

        $response = $this->actingAs($this->owner)->postJson('/settings/supervisors/invite', [
            'email' => $this->supervisor->email,
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);
    });
});

describe('Aceite e Recusa de Convite', function () {
    test('supervisor pode aceitar convite e acesso e criado', function () {
        $invitation = SupervisorInvitation::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'can_view_financials' => true,
            'can_view_analytics' => true,
            'can_export' => false,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->supervisor)
            ->postJson("/supervisor/invitations/{$invitation->uuid}/accept");

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('supervisor_accesses', [
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'can_view_financials' => true,
            'can_view_analytics' => true,
            'can_export' => false,
        ]);

        $this->assertDatabaseHas('supervisor_invitations', [
            'id' => $invitation->id,
            'status' => 'accepted',
        ]);
    });

    test('supervisor pode recusar convite', function () {
        $invitation = SupervisorInvitation::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->supervisor)
            ->postJson("/supervisor/invitations/{$invitation->uuid}/reject");

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('supervisor_invitations', [
            'id' => $invitation->id,
            'status' => 'rejected',
        ]);
        $this->assertDatabaseMissing('supervisor_accesses', [
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
        ]);
    });

    test('terceiro nao pode aceitar convite de outro', function () {
        $outro = makePremiumUser();
        $invitation = SupervisorInvitation::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($outro)
            ->postJson("/supervisor/invitations/{$invitation->uuid}/accept");

        $response->assertStatus(403);
    });

    test('acesso aceito herda expires_at do convite', function () {
        $expiresAt = now()->addMonth();
        $invitation = SupervisorInvitation::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'expires_at' => $expiresAt,
            'status' => 'pending',
        ]);

        $this->actingAs($this->supervisor)
            ->postJson("/supervisor/invitations/{$invitation->uuid}/accept");

        $access = SupervisorAccess::where('user_id', $this->owner->id)
            ->where('supervisor_id', $this->supervisor->id)
            ->first();

        expect($access->expires_at)->not->toBeNull();
        expect($access->expires_at->format('Y-m-d'))->toBe($expiresAt->format('Y-m-d'));
    });
});

describe('Controle de Acesso ao Dashboard', function () {
    test('supervisor com acesso pode ver dashboard do usuario', function () {
        $access = SupervisorAccess::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'can_view_financials' => false,
            'can_view_analytics' => false,
            'can_export' => false,
        ]);

        $response = $this->actingAs($this->supervisor)
            ->get("/supervisor/{$access->uuid}");

        $response->assertStatus(200);
    });

    test('supervisor sem acesso recebe redirecionamento', function () {
        $outroOwner = makePremiumUser();
        $access = SupervisorAccess::create([
            'user_id' => $outroOwner->id,
            'supervisor_id' => $outroOwner->id, // acesso de outro par
            'can_view_financials' => false,
            'can_view_analytics' => false,
            'can_export' => false,
        ]);

        // supervisor tenta acessar UUID de acesso que não é dele
        $response = $this->actingAs($this->supervisor)
            ->get("/supervisor/{$access->uuid}");

        $response->assertRedirect(route('supervisor.index'));
    });

    test('acesso expirado resulta em redirecionamento', function () {
        $access = SupervisorAccess::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->supervisor)
            ->get("/supervisor/{$access->uuid}");

        $response->assertRedirect(route('supervisor.index'));
    });
});

describe('Revogacao de Acesso', function () {
    test('usuario pode revogar acesso de supervisor', function () {
        $access = SupervisorAccess::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->deleteJson("/settings/supervisors/{$access->uuid}");

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('supervisor_accesses', ['id' => $access->id]);
    });

    test('usuario nao pode revogar acesso de outro usuario', function () {
        $outroOwner = makePremiumUser();
        $access = SupervisorAccess::create([
            'user_id' => $outroOwner->id,
            'supervisor_id' => $this->supervisor->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->deleteJson("/settings/supervisors/{$access->uuid}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('supervisor_accesses', ['id' => $access->id]);
    });
});

describe('Banner de Convites Pendentes', function () {
    test('dashboard exibe banner quando ha convites pendentes', function () {
        SupervisorInvitation::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->supervisor)->get('/dashboard');

        $response->assertStatus(200)
            ->assertSee('convite');
    });

    test('dashboard nao exibe banner quando nao ha convites pendentes', function () {
        $response = $this->actingAs($this->supervisor)->get('/dashboard');

        $response->assertStatus(200)
            ->assertDontSee('convites de supervisão pendentes');
    });
});

describe('Exportacao pelo Supervisor', function () {
    test('supervisor com can_export pode baixar pdf', function () {
        $access = SupervisorAccess::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'can_export' => true,
        ]);

        $response = $this->actingAs($this->supervisor)
            ->get("/supervisor/{$access->uuid}/export/pdf?month=2026-03");

        $response->assertStatus(200);
    });

    test('supervisor sem can_export recebe 403 no pdf', function () {
        $access = SupervisorAccess::create([
            'user_id' => $this->owner->id,
            'supervisor_id' => $this->supervisor->id,
            'can_export' => false,
        ]);

        $response = $this->actingAs($this->supervisor)
            ->get("/supervisor/{$access->uuid}/export/pdf?month=2026-03");

        $response->assertStatus(403);
    });
});
