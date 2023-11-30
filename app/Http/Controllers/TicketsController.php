<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;

class TicketsController extends Controller
{
    const DEFAULT_PAGINATION = 10;

    public function index()
    {
        $user = Auth::user();
        $tickets = $user->tickets()
            ->with(['category', 'user', 'resolver'])
            ->orderByDesc('id')
            ->simplePaginate(self::DEFAULT_PAGINATION);

        return view('tickets.index', ['tickets' => $tickets]);
    }

    public function create($type = null)
    {
        $type = $type ? Type::MAP[$type] : Type::DEFAULT;
        $priorities = array_reverse(Ticket::PRIORITIES);

        return view('tickets.create', [
            'type' => $type,
            'categories' => Category::all(),
            'priorities' => $priorities,
            'default_priority' => Ticket::DEFAULT_PRIORITY,
        ]);
    }

    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);

        $this->authorize('edit', $ticket);

        return view('tickets.edit', [
            'ticket' => $ticket,
        ]);
    }
}
