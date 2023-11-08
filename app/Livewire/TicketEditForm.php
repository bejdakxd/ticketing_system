<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\OnHoldReason;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;

class TicketEditForm extends TicketForm
{
    public Ticket $ticket;
    public $status;
    public $onHoldReason;
    public $priority;
    public $group;
    public $resolver;

    public Collection $statuses;
    public Collection $onHoldReasons;
    public array $priorities;
    public Collection $groups;
    public Collection $resolvers;

    public function rules()
    {
        return [
            'status' => 'min:1|max:'. Status::count() . '|required|numeric',
            'onHoldReason' => 'min:1|max:'. OnHoldReason::count() . '|required_if:status,'. Status::ON_HOLD . '|nullable|numeric',
            'priority' => 'min:1|max:'. count(Ticket::PRIORITIES) . '|required|numeric',
            'group' => 'min:1|max:'. Group::count() . '|required|numeric',
            'resolver' => 'min:1|max:'. User::max('id') . '|nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'onHoldReason.required_if' => 'On hold reason is required',
        ];
    }

    public function mount(Ticket $ticket){
        $this->ticket = $ticket;
        $this->status = $this->ticket->status_id;
        $this->onHoldReason = $this->ticket->on_hold_reason_id;
        $this->priority = $this->ticket->priority;
        $this->group = $this->ticket->group_id;
        $this->resolver = $this->ticket->resolver_id;

        $this->statuses = Status::all();
        $this->onHoldReasons = OnHoldReason::all();
        $this->priorities = Ticket::PRIORITIES;
        $this->groups = Group::all();
        $this->resolvers = Group::find($this->group)->resolvers()->get();
    }

    public function render()
    {
        return view('livewire.ticket-edit-form');
    }

    public function updated($property): void
    {
        $this->syncTicket();

        parent::updated($property);

        if($property === 'group'){
            $this->resolver = null;
        }
        if($property === 'status' &&  !$this->ticket->isStatus('on_hold')){
            $this->onHoldReason = null;
        }
    }

    public function save()
    {
        $this->syncTicket();
        $this->validate();
        $this->ticket->save();
    }

    protected function checkOnHoldReason(){
        if($this->ticket->isStatus('on_hold') && $this->onHoldReason === null){
            abort(403);
        }
    }

    protected function syncTicket(){
        $this->ticket->status_id = $this->status;
        $this->ticket->on_hold_reason_id = $this->onHoldReason;
        $this->ticket->priority = $this->priority;
        $this->ticket->group_id = $this->group;
        $this->ticket->resolver_id = $this->resolver;
        $this->resolvers = $this->ticket->group ? $this->ticket->group->resolvers : collect([]);
    }
}
