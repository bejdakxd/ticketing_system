<?php

namespace App\Policies;

use App\Models\Resolver;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {

    }

    public function show(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id;
    }

    public function edit(User $user, Ticket $ticket)
    {
        return ($user->id === $ticket->user_id || $user->hasPermissionTo('view_all_tickets'));
    }

    public function update(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id;
    }

    public function destroy(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id;
    }

    public function setPriority(User $user, Ticket $ticket)
    {
        return (bool) $user->can('set_priority');
    }

    public function setResolver(User $user, Ticket $ticket)
    {
        return (bool) $user->can('set_resolver');
    }

    public function addComment(User $user, Ticket $ticket)
    {
        return (bool) ($user->id === $ticket->user_id || $user->hasPermissionTo('add_comments_to_all_tickets'));
    }
}
