<?php

use App\Models\Changelog;
use App\Models\ChangelogItem;
use App\Models\ChangelogRead;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->admin = User::factory()->create(['is_admin' => true]);

    Setting::create([
        'user_id' => $this->user->id,
        'hourly_rate' => 100,
        'extra_value' => 0,
        'discount_value' => 0,
    ]);

    Setting::create([
        'user_id' => $this->admin->id,
        'hourly_rate' => 100,
        'extra_value' => 0,
        'discount_value' => 0,
    ]);
});

describe('Admin: Criar Changelog', function () {
    test('admin pode criar changelog com itens', function () {
        $response = $this->actingAs($this->admin)->postJson('/admin/changelogs', [
            'title' => 'Nova Funcionalidade',
            'version' => '1.0.0',
            'notification_style' => 'badge',
            'is_published' => false,
            'items' => [
                ['category' => 'feature', 'description' => 'Adicionamos X ao sistema.'],
            ],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('changelogs', ['title' => 'Nova Funcionalidade']);
        $this->assertDatabaseHas('changelog_items', ['category' => 'feature', 'description' => 'Adicionamos X ao sistema.']);
    });

    test('admin pode criar changelog publicado', function () {
        $response = $this->actingAs($this->admin)->postJson('/admin/changelogs', [
            'title' => 'Publicado Imediatamente',
            'notification_style' => 'modal',
            'is_published' => true,
            'items' => [
                ['category' => 'improvement', 'description' => 'Conteúdo.'],
            ],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $changelog = Changelog::where('title', 'Publicado Imediatamente')->first();
        expect($changelog->is_published)->toBeTrue();
        expect($changelog->published_at)->not->toBeNull();
    });

    test('admin pode criar changelog com múltiplos itens de categorias diferentes', function () {
        $response = $this->actingAs($this->admin)->postJson('/admin/changelogs', [
            'title' => 'Release v2.0',
            'version' => '2.0.0',
            'notification_style' => 'both',
            'is_published' => true,
            'items' => [
                ['category' => 'feature', 'description' => 'Nova tela de analytics.', 'sort_order' => 0],
                ['category' => 'improvement', 'description' => 'Dashboard mais rápido.', 'sort_order' => 1],
                ['category' => 'bugfix', 'description' => 'Correção no cálculo de horas.', 'sort_order' => 2],
                ['category' => 'hotfix', 'description' => 'Correção urgente no login.', 'sort_order' => 3],
            ],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $changelog = Changelog::where('title', 'Release v2.0')->first();
        expect($changelog->items->count())->toBe(4);
        expect($changelog->items->pluck('category')->toArray())->toBe(['feature', 'improvement', 'bugfix', 'hotfix']);
    });

    test('categoria hotfix é aceita', function () {
        $response = $this->actingAs($this->admin)->postJson('/admin/changelogs', [
            'title' => 'Hotfix urgente',
            'notification_style' => 'badge',
            'is_published' => false,
            'items' => [
                ['category' => 'hotfix', 'description' => 'Correção crítica aplicada.'],
            ],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('changelog_items', ['category' => 'hotfix']);
    });

    test('usuário comum não pode criar changelog (403)', function () {
        $response = $this->actingAs($this->user)->postJson('/admin/changelogs', [
            'title' => 'Teste',
            'notification_style' => 'badge',
            'items' => [['category' => 'feature', 'description' => 'Conteúdo.']],
        ]);

        $response->assertStatus(403);
    });

    test('visitante não autenticado não pode criar changelog', function () {
        $response = $this->postJson('/admin/changelogs', [
            'title' => 'Teste',
            'notification_style' => 'badge',
            'items' => [['category' => 'feature', 'description' => 'Conteúdo.']],
        ]);

        // Rota requer auth, então retorna 401 antes mesmo de verificar is_admin
        $response->assertStatus(401);
    });
});

describe('Admin: Editar e Deletar Changelog', function () {
    test('admin pode editar changelog', function () {
        $changelog = Changelog::create([
            'title' => 'Original',
            'notification_style' => 'badge',
            'is_published' => false,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/changelogs/{$changelog->id}", [
            'title' => 'Atualizado',
            'notification_style' => 'both',
            'is_published' => true,
            'items' => [
                ['category' => 'improvement', 'description' => 'Novo conteúdo.'],
            ],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('changelogs', ['title' => 'Atualizado', 'is_published' => true]);
        $this->assertDatabaseHas('changelog_items', ['category' => 'improvement', 'description' => 'Novo conteúdo.']);
    });

    test('editar substitui os itens antigos pelos novos', function () {
        $changelog = Changelog::create([
            'title' => 'Com Itens',
            'notification_style' => 'badge',
            'is_published' => false,
        ]);
        ChangelogItem::create([
            'changelog_id' => $changelog->id,
            'category' => 'feature',
            'description' => 'Item antigo.',
            'sort_order' => 0,
        ]);

        $this->actingAs($this->admin)->putJson("/admin/changelogs/{$changelog->id}", [
            'title' => 'Com Itens',
            'notification_style' => 'badge',
            'is_published' => false,
            'items' => [
                ['category' => 'bugfix', 'description' => 'Item novo.'],
            ],
        ]);

        $this->assertDatabaseMissing('changelog_items', ['description' => 'Item antigo.']);
        $this->assertDatabaseHas('changelog_items', ['description' => 'Item novo.']);
    });

    test('publicar define published_at automaticamente', function () {
        $changelog = Changelog::create([
            'title' => 'Rascunho',
            'notification_style' => 'badge',
            'is_published' => false,
        ]);

        $this->actingAs($this->admin)->putJson("/admin/changelogs/{$changelog->id}", [
            'title' => 'Rascunho',
            'notification_style' => 'badge',
            'is_published' => true,
            'items' => [
                ['category' => 'bugfix', 'description' => 'Conteúdo.'],
            ],
        ]);

        $changelog->refresh();
        expect($changelog->is_published)->toBeTrue();
        expect($changelog->published_at)->not->toBeNull();
    });

    test('despublicar limpa published_at', function () {
        $changelog = Changelog::create([
            'title' => 'Publicado',
            'notification_style' => 'badge',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        $this->actingAs($this->admin)->putJson("/admin/changelogs/{$changelog->id}", [
            'title' => 'Publicado',
            'notification_style' => 'badge',
            'is_published' => false,
            'items' => [
                ['category' => 'feature', 'description' => 'Conteúdo.'],
            ],
        ]);

        $changelog->refresh();
        expect($changelog->is_published)->toBeFalse();
        expect($changelog->published_at)->toBeNull();
    });

    test('admin pode deletar changelog', function () {
        $changelog = Changelog::create([
            'title' => 'Para Deletar',
            'notification_style' => 'badge',
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/admin/changelogs/{$changelog->id}");

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('changelogs', ['id' => $changelog->id]);
    });

    test('usuário comum não pode deletar changelog (403)', function () {
        $changelog = Changelog::create([
            'title' => 'Não Deletável',
            'notification_style' => 'badge',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/admin/changelogs/{$changelog->id}");
        $response->assertStatus(403);
    });
});

describe('Admin: Listar Changelogs', function () {
    test('admin pode acessar index de changelogs', function () {
        $response = $this->actingAs($this->admin)->get('/admin/changelogs');
        $response->assertStatus(200);
    });

    test('usuário comum não pode acessar admin (403)', function () {
        $response = $this->actingAs($this->user)->get('/admin/changelogs');
        $response->assertStatus(403);
    });
});

describe('Marcar Changelog como Lido', function () {
    test('usuário pode marcar changelog como lido', function () {
        $changelog = Changelog::create([
            'title' => 'Novidade',
            'notification_style' => 'badge',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->user)->postJson("/changelogs/{$changelog->id}/read");

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('changelog_reads', [
            'user_id' => $this->user->id,
            'changelog_id' => $changelog->id,
        ]);
    });

    test('marcar como lido duas vezes não duplica registro', function () {
        $changelog = Changelog::create([
            'title' => 'Novidade',
            'notification_style' => 'badge',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        $this->actingAs($this->user)->postJson("/changelogs/{$changelog->id}/read");
        $this->actingAs($this->user)->postJson("/changelogs/{$changelog->id}/read");

        $count = ChangelogRead::where('user_id', $this->user->id)
            ->where('changelog_id', $changelog->id)
            ->count();

        expect($count)->toBe(1);
    });
});

describe('Contagem de Não Lidos', function () {
    test('retorna count correto de changelogs não lidos', function () {
        Changelog::create([
            'title' => 'Novidade 1',
            'notification_style' => 'badge',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);
        Changelog::create([
            'title' => 'Novidade 2',
            'notification_style' => 'modal',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->user)->getJson('/changelogs/unread-count');
        $response->assertStatus(200)->assertJson(['count' => 2]);
    });

    test('changelog já lido não conta como não lido', function () {
        $changelog = Changelog::create([
            'title' => 'Novidade Lida',
            'notification_style' => 'badge',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        ChangelogRead::create([
            'user_id' => $this->user->id,
            'changelog_id' => $changelog->id,
            'read_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->user)->getJson('/changelogs/unread-count');
        $response->assertStatus(200)->assertJson(['count' => 0]);
    });

    test('changelog não publicado não aparece para usuários', function () {
        Changelog::create([
            'title' => 'Rascunho',
            'notification_style' => 'badge',
            'is_published' => false,
        ]);

        $response = $this->actingAs($this->user)->getJson('/changelogs/unread-count');
        $response->assertStatus(200)->assertJson(['count' => 0]);
    });

    test('reads de um usuário não afetam contagem de outro', function () {
        $changelog = Changelog::create([
            'title' => 'Novidade Isolada',
            'notification_style' => 'badge',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        // Admin leu
        ChangelogRead::create([
            'user_id' => $this->admin->id,
            'changelog_id' => $changelog->id,
            'read_at' => Carbon::now(),
        ]);

        // Usuário ainda não leu
        $response = $this->actingAs($this->user)->getJson('/changelogs/unread-count');
        $response->assertStatus(200)->assertJson(['count' => 1]);
    });
});

describe('Marcar Todos como Lidos', function () {
    test('marcar todos como lidos funciona corretamente', function () {
        $c1 = Changelog::create([
            'title' => 'A',
            'notification_style' => 'badge',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);
        $c2 = Changelog::create([
            'title' => 'B',
            'notification_style' => 'modal',
            'is_published' => true,
            'published_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->user)->postJson('/changelogs/mark-all-read', [
            'ids' => [$c1->id, $c2->id],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $count = ChangelogRead::where('user_id', $this->user->id)->count();
        expect($count)->toBe(2);
    });
});
