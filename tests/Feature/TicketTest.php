<?php

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Mail\TicketOperatorNotificationMail;
use App\Mail\TicketRepliedMail;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create(['is_admin' => true]);

    Setting::create([
        'user_id'        => $this->user->id,
        'hourly_rate'    => 100,
        'extra_value'    => 0,
        'discount_value' => 0,
    ]);
});

describe('Abertura de Chamado', function () {
    test('usuário autenticado pode abrir chamado', function () {
        $response = $this->actingAs($this->user)->postJson('/tickets', [
            'title'    => 'Problema no sistema',
            'category' => TicketCategory::Bug->value,
            'body'     => 'O sistema está apresentando um erro.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'user_id'  => $this->user->id,
            'title'    => 'Problema no sistema',
            'status'   => TicketStatus::Open->value,
        ]);
    });

    test('primeira mensagem é criada junto com o chamado', function () {
        $this->actingAs($this->user)->postJson('/tickets', [
            'title'    => 'Meu chamado',
            'category' => TicketCategory::Question->value,
            'body'     => 'Minha dúvida aqui.',
        ]);

        $ticket = Ticket::where('user_id', $this->user->id)->first();
        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id'   => $ticket->id,
            'user_id'     => $this->user->id,
            'body'        => 'Minha dúvida aqui.',
            'is_internal' => false,
        ]);
    });

    test('título é obrigatório', function () {
        $response = $this->actingAs($this->user)->postJson('/tickets', [
            'category' => TicketCategory::Bug->value,
            'body'     => 'Descrição',
        ]);
        $response->assertJsonValidationErrors(['title']);
    });

    test('categoria inválida é rejeitada', function () {
        $response = $this->actingAs($this->user)->postJson('/tickets', [
            'title'    => 'Título',
            'category' => 'invalida',
            'body'     => 'Descrição',
        ]);
        $response->assertJsonValidationErrors(['category']);
    });

    test('usuário não autenticado é redirecionado', function () {
        $this->post('/tickets', [])->assertRedirect('/login');
    });
});

describe('Visualização de Chamados', function () {
    test('usuário vê apenas seus próprios chamados', function () {
        $other = User::factory()->create();
        Ticket::create(['user_id' => $this->user->id, 'title' => 'Meu chamado', 'category' => 'bug', 'status' => 'open']);
        Ticket::create(['user_id' => $other->id, 'title' => 'Chamado alheio', 'category' => 'bug', 'status' => 'open']);

        $response = $this->actingAs($this->user)->get('/tickets');
        $response->assertStatus(200)->assertSee('Meu chamado')->assertDontSee('Chamado alheio');
    });

    test('usuário pode ver detalhe do próprio chamado', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'Meu chamado', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->user)->get("/tickets/{$ticket->id}")->assertStatus(200);
    });

    test('usuário não pode ver chamado de outro usuário', function () {
        $other = User::factory()->create();
        $ticket = Ticket::create(['user_id' => $other->id, 'title' => 'Alheio', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->user)->get("/tickets/{$ticket->id}")->assertStatus(403);
    });
});

describe('Mensagens no Chamado', function () {
    test('usuário pode enviar mensagem no próprio chamado', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->user)
            ->post("/tickets/{$ticket->id}/messages", ['body' => 'Minha resposta'])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'is_internal' => false]);
    });

    test('mensagem do usuário nunca é interna', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->user)
            ->post("/tickets/{$ticket->id}/messages", ['body' => 'Texto', 'is_internal' => true]);

        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'is_internal' => false]);
    });

    test('usuário não pode enviar mensagem em chamado encerrado', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'closed']);
        $response = $this->actingAs($this->user)
            ->post("/tickets/{$ticket->id}/messages", ['body' => 'Texto']);
        $response->assertSessionHasErrors();
    });

    test('usuário não pode enviar mensagem em chamado alheio', function () {
        $other = User::factory()->create();
        $ticket = Ticket::create(['user_id' => $other->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->user)
            ->post("/tickets/{$ticket->id}/messages", ['body' => 'Texto'])
            ->assertStatus(403);
    });
});

describe('Encerramento pelo Usuário', function () {
    test('usuário pode encerrar próprio chamado', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->user)->post("/tickets/{$ticket->id}/close")->assertRedirect();
        $this->assertEquals('closed', $ticket->fresh()->status->value);
    });

    test('usuário não pode encerrar chamado alheio', function () {
        $other = User::factory()->create();
        $ticket = Ticket::create(['user_id' => $other->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->user)->post("/tickets/{$ticket->id}/close")->assertStatus(403);
    });
});

describe('Admin — Listagem', function () {
    test('admin vê todos os chamados', function () {
        $other = User::factory()->create();
        Ticket::create(['user_id' => $this->user->id, 'title' => 'Do user', 'category' => 'bug', 'status' => 'open']);
        Ticket::create(['user_id' => $other->id, 'title' => 'Do other', 'category' => 'bug', 'status' => 'open']);

        $this->actingAs($this->admin)->get('/admin/tickets')
            ->assertStatus(200)
            ->assertSee('Do user')
            ->assertSee('Do other');
    });

    test('não-admin não acessa painel de chamados', function () {
        $this->actingAs($this->user)->get('/admin/tickets')->assertStatus(403);
    });
});

describe('Admin — Assumir Chamado', function () {
    test('admin pode assumir chamado e torna-se operador', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->admin)->post("/admin/tickets/{$ticket->id}/assign")->assertRedirect();
        $this->assertEquals($this->admin->id, $ticket->fresh()->operator_id);
    });

    test('status muda para in_analysis ao assumir chamado aberto', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->admin)->post("/admin/tickets/{$ticket->id}/assign");
        $this->assertEquals('in_analysis', $ticket->fresh()->status->value);
    });
});

describe('Admin — Status', function () {
    test('admin pode mudar status para waiting_client', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->admin)
            ->putJson("/admin/tickets/{$ticket->id}/status", ['status' => 'waiting_client'])
            ->assertJson(['success' => true]);
        $this->assertEquals('waiting_client', $ticket->fresh()->status->value);
    });

    test('status inválido retorna erro de validação', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->admin)
            ->putJson("/admin/tickets/{$ticket->id}/status", ['status' => 'invalido'])
            ->assertJsonValidationErrors(['status']);
    });
});

describe('Admin — Resposta', function () {
    test('admin pode responder com mensagem pública', function () {
        Mail::fake();
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->admin)
            ->post("/admin/tickets/{$ticket->id}/messages", ['body' => 'Resposta pública', 'is_internal' => 0])
            ->assertRedirect();
        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'is_internal' => false]);
    });

    test('admin pode responder com nota interna', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        $this->actingAs($this->admin)
            ->post("/admin/tickets/{$ticket->id}/messages", ['body' => 'Nota interna', 'is_internal' => 1])
            ->assertRedirect();
        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'is_internal' => true]);
    });

    test('mensagem interna não aparece na view do usuário', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->admin->id, 'body' => 'Nota secreta', 'is_internal' => true]);

        $response = $this->actingAs($this->user)->get("/tickets/{$ticket->id}");
        $response->assertStatus(200)->assertDontSee('Nota secreta');
    });
});

describe('Emails', function () {
    test('usuário recebe email quando operador responde publicamente', function () {
        Mail::fake();
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open', 'operator_id' => null]);

        $this->actingAs($this->admin)
            ->post("/admin/tickets/{$ticket->id}/messages", ['body' => 'Olá, vamos resolver!', 'is_internal' => 0]);

        Mail::assertSent(TicketRepliedMail::class, fn($mail) => $mail->hasTo($this->user->email));
    });

    test('email não é enviado para nota interna', function () {
        Mail::fake();
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);

        $this->actingAs($this->admin)
            ->post("/admin/tickets/{$ticket->id}/messages", ['body' => 'Nota interna', 'is_internal' => 1]);

        Mail::assertNotSent(TicketRepliedMail::class);
    });

    test('operador recebe email quando cliente responde', function () {
        Mail::fake();
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open', 'operator_id' => $this->admin->id]);

        $this->actingAs($this->user)
            ->post("/tickets/{$ticket->id}/messages", ['body' => 'Mais informações aqui.']);

        Mail::assertSent(TicketOperatorNotificationMail::class, fn($mail) => $mail->hasTo($this->admin->email));
    });

    test('email para operador não é enviado se não há responsável', function () {
        Mail::fake();
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open', 'operator_id' => null]);

        $this->actingAs($this->user)
            ->post("/tickets/{$ticket->id}/messages", ['body' => 'Mensagem sem responsável.']);

        Mail::assertNotSent(TicketOperatorNotificationMail::class);
    });
});

describe('Badge de Nova Resposta', function () {
    test('has_unread_reply é true quando operador respondeu após último usuário', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->user->id, 'body' => 'Abri', 'is_internal' => false]);
        $this->travel(2)->seconds();
        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->admin->id, 'body' => 'Respondi', 'is_internal' => false]);

        $response = $this->actingAs($this->user)->get('/tickets');
        $response->assertStatus(200)->assertSee('Nova resposta');
    });

    test('has_unread_reply é false quando usuário respondeu por último', function () {
        $ticket = Ticket::create(['user_id' => $this->user->id, 'title' => 'T', 'category' => 'bug', 'status' => 'open']);
        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->admin->id, 'body' => 'Admin', 'is_internal' => false]);
        $this->travel(2)->seconds();
        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $this->user->id, 'body' => 'User', 'is_internal' => false]);

        $response = $this->actingAs($this->user)->get('/tickets');
        $response->assertStatus(200)->assertDontSee('Nova resposta');
    });
});
