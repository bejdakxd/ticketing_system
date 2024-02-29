<?php

namespace App\Livewire\Tables;

use App\Helpers\Columns\Column;
use App\Helpers\Columns\ColumnRoute;
use App\Helpers\Columns\Columns;
use App\Models\ConfigurationItem;
use Illuminate\Database\Eloquent\Builder;

class ConfigurationItemsTable extends Table
{
    function query(): Builder
    {
        return ConfigurationItem::query()->with('user');
    }

    function columns(): Columns
    {
        return Columns::create(
            Column::create('Serial Number', 'serial_number', ColumnRoute::create('userConfiguration-items.edit', ['id'])),
            Column::create('User', 'user.name'),
            Column::create('Location', 'location.value'),
        );
    }
}
