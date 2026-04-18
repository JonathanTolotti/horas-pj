<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketMessageRequest;
use App\Mail\TicketRepliedMail;
use App\Mail\TicketStatusChangedMail;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Enum;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'operator'])->latest('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->paginate(20)->withQueryString();

        $counts = [];
        foreach (TicketStatus::cases() as $status) {
            $counts[$status->value] = Ticket::where('status', $status->value)->count();
        }

        $statuses   = TicketStatus::cases();
        $categories = \App\Enums\TicketCategory::cases();

        return view('admin.tickets.index', compact('tickets', 'counts', 'statuses', 'categories'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('messages.user', 'user', 'operator');
        $statuses = TicketStatus::cases();

        return view('admin.tickets.show', compact('ticket', 'statuses'));
    }

    public function reply(StoreTicketMessageRequest $request, Ticket $ticket)
    {
        $isInternal = (bool) $request->input('is_internal', false);

        $message = $ticket->messages()->create([
            'user_id'     => auth()->id(),
            'body'        => $request->body,
            'is_internal' => $isInternal,
        ]);

        if (!$isInternal) {
            $ticket->touch();

            // Notificar cliente por email
            $ticket->load('user');
            Mail::to($ticket->user->email)
                ->send(new TicketRepliedMail($ticket, $message));
        }

        return back()->with('success', 'Resposta enviada.');
    }

    public function assign(Ticket $ticket)
    {
        if ($ticket->operator_id === auth()->id()) {
            return back()->with('info', 'Você já é o responsável por este chamado.');
        }

        $ticket->operator_id = auth()->id();

        if ($ticket->status === TicketStatus::Open) {
            $ticket->status = TicketStatus::InAnalysis;
        }

        $ticket->save();

        return back()->with('success', 'Chamado assumido com sucesso.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', new Enum(TicketStatus::class)],
        ]);

        $oldStatus = $ticket->status;
        $newStatus = TicketStatus::from($request->status);

        $ticket->update(['status' => $request->status]);

        if ($oldStatus !== $newStatus) {
            $ticket->load('user');
            Mail::to($ticket->user->email)
                ->send(new TicketStatusChangedMail($ticket));
        }

        return response()->json(['success' => true, 'label' => $newStatus->label()]);
    }
}
