<?php

namespace App\Livewire\Tables;

use App\Helpers\Table\TableBuilder;
use App\Livewire\Table;
use App\Models\Request;
use Illuminate\Database\Eloquent\Builder;

class RequestsTable extends Table
{
    function query(): Builder
    {
        return Request::query()->with('caller');
    }

    function schema(): TableBuilder
    {
        return $this->tableBuilder()
            ->column('Number', 'id', ['requests.edit', 'id'])
            ->column('Description', 'description')
            ->column('Caller', 'caller.name')
            ->column('Resolver', 'resolver.name')
            ->column('Status', 'status.value')
            ->column('Priority', 'priority.value');
    }
}
