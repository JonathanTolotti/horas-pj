<?php

namespace App\Http\Controllers;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketMessageRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Mail\TicketOperatorNotificationMail;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::forUser(auth()->id())
            ->with(['messages' => fn($q) => $q->where('is_internal', false), 'operator'])
            ->latest('updated_at')
            ->get();

        foreach ($tickets as $ticket) {
            $lastUserMsg     = $ticket->messages->where('user_id', auth()->id())->max('created_at');
            $lastOperatorMsg = $ticket->messages->where('user_id', '!=', auth()->id())->max('created_at');
            $ticket->has_unread_reply = $lastOperatorMsg && (!$lastUserMsg || $lastOperatorMsg > $lastUserMsg);
        }

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = TicketCategory::cases();
        return view('tickets.create', compact('categories'));
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create([
            'user_id'  => auth()->id(),
            'title'    => $request->title,
            'category' => $request->category,
            'status'   => TicketStatus::Open,
        ]);

        $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'body'        => $request->body,
            'is_internal' => false,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Chamado aberto com sucesso.');
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->isOwnedBy(auth()->user()), 403);

        $ticket->load('publicMessages.user', 'operator');

        return view('tickets.show', compact('ticket'));
    }

    public function addMessage(StoreTicketMessageRequest $request, Ticket $ticket)
    {
        abort_unless($ticket->isOwnedBy(auth()->user()), 403);

        if ($ticket->isClosed()) {
            return back()->withErrors(['body' => 'Este chamado está encerrado e não aceita novas mensagens.']);
        }

        $message = $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'body'        => $request->body,
            'is_internal' => false,
        ]);

        $ticket->touch();

        // Notificar operador por email se houver um responsável
        if ($ticket->operator_id) {
            $ticket->load('operator');
            Mail::to($ticket->operator->email)
                ->send(new TicketOperatorNotificationMail($ticket, $message));
        }

        return back()->with('success', 'Mensagem enviada.');
    }

    public function close(Ticket $ticket)
    {
        abort_unless($ticket->isOwnedBy(auth()->user()), 403);

        $ticket->update(['status' => TicketStatus::Closed]);

        return back()->with('success', 'Chamado encerrado.');
    }
}
