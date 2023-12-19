<?php

use App\Models\Group;
use App\Models\Request\Request;
use App\Models\Request\RequestCategory;
use App\Models\Request\RequestItem;
use App\Models\Request\RequestOnHoldReason;
use App\Models\Request\RequestStatus;
use App\Models\Status;
use App\Models\Ticket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caller_id')->references('id')->on('users');
            $table->foreignId('resolver_id')->nullable()->constrained()->references('id')->on('users');
            $table->text('description');
            $table->enum('category_id', RequestCategory::MAP);
            $table->enum('item_id', RequestItem::MAP);
            $table->enum('status_id', Status::MAP);
            $table->enum('on_hold_reason_id', RequestOnHoldReason::MAP)->nullable();
            $table->enum('group_id', Group::MAP);
            $table->enum('priority', Ticket::PRIORITIES);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
