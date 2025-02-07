<?php

namespace Database\Seeders;

use App\Helpers\Config;
use App\Models\Request;
use App\Models\Request\RequestCategory;
use App\Models\Request\RequestItem;
use App\Models\TicketConfig;
use Illuminate\Database\Seeder;

class RequestCategoryRequestItemSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Request::CATEGORY_TO_ITEM as $value){
            $category = RequestCategory::findOrFail($value[0]);
            $item = RequestItem::findOrFail($value[1]);
            $category->items()->attach($item);
        }
    }
}
