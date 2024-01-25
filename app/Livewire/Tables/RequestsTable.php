<?php

namespace App\Livewire\Tables;

use App\Enums\SortOrder;
use App\Helpers\Table\Table;
use App\Models\Request;

class RequestsTable extends \App\Livewire\Table
{
    public string $columnToSortBy = 'id';
    public SortOrder $sortOrder = SortOrder::DESCENDING;

    function table(): Table
    {
        return Table::make(Request::query()->with('caller'))
            ->sortByColumn($this->columnToSortBy)
            ->sortOrder($this->sortOrder)
            ->column('Number', 'id', ['requests.edit', 'id'])
            ->column('Caller', 'caller.name')
            ->column('Resolver', 'resolver.name')
            ->column('Status', 'status.value')
            ->column('Priority', 'priority.value')
            ->get();
    }
}
