<?php

namespace App\Models;

use App\Enums\TaskSequence;
use App\Interfaces\Ticket;
use App\Observers\TicketObserver;
use App\Traits\HasSla;
use App\Traits\TicketTrait;
use App\Interfaces\Activitable;
use App\Interfaces\Fieldable;
use App\Interfaces\Slable;
use App\Models\Request\Request;
use App\Models\Request\RequestCategory;
use App\Models\Request\RequestItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Task extends Model implements Ticket, Slable, Fieldable, Activitable
{
    use HasSla, HasFactory, TicketTrait;

    protected $guarded = [];
    protected $casts = [
        'resolved_at' => 'datetime',
        'task_sequence' => TaskSequence::class,
    ];
    protected $attributes = [
        'status_id' => self::DEFAULT_STATUS,
        'group_id' => self::DEFAULT_GROUP,
        'priority' => self::DEFAULT_PRIORITY,
    ];

    const PRIORITY_TO_SLA_MINUTES = [
        1 => 30,
        2 => 2 * 60,
        3 => 12 * 60,
        4 => 24 * 60,
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::observe(TicketObserver::class);
    }

    function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    function category(): HasOneThrough
    {
        return $this->hasOneThrough(
            RequestCategory::class,
            Request::class,
            'category_id',
            'id',
            'request_id',
            'id'
        );
    }

    function item(): HasOneThrough
    {
        return $this->hasOneThrough(
            RequestItem::class,
            Request::class,
            'item_id',
            'id',
            'request_id',
            'id'
        );
    }

    public function isArchived(): bool{
        return
            $this->getOriginal('status_id') == Status::RESOLVED ||
            $this->getOriginal('status_id') == Status::CANCELLED
        ;
    }

}
