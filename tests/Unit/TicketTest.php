<?php


namespace Tests\Unit;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Item;
use App\Models\OnHoldReason;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketConfig;
use App\Models\Type;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    function test_it_has_one_type()
    {
        $type = Type::factory(['name' => 'incident'])->create();
        $ticket = Ticket::factory(['type_id' => $type])->create();

        $this->assertEquals('Incident', $ticket->type->name);
    }

    function test_it_has_one_category()
    {
        $category = Category::findOrFail(Category::NETWORK);
        $ticket = Ticket::factory(['category_id' => $category])->create();

        $this->assertEquals('Network', $ticket->category->name);
    }

    function test_it_has_one_resolver(){
        $resolver = User::factory(['name' => 'John Doe'])->create()->assignRole('resolver');
        $ticket = Ticket::factory(['resolver_id' => $resolver])->create();

        $this->assertEquals('John Doe', $ticket->resolver->name);
    }

    public function test_it_has_many_comments()
    {
        $ticket = Ticket::factory()->create();

        $commentOne = Comment::factory([
            'ticket_id' => $ticket,
            'body' => 'Comment Body 1',
        ])->create();

        $commentTwo = Comment::factory()->create([
            'ticket_id' => $ticket,
            'body' => 'Comment Body 2'
        ]);

        $i = 1;
        foreach ($ticket->comments as $comment){
            $this->assertEquals('Comment Body ' . $i, $comment->body);
            $i++;
        }
    }

    public function test_it_belongs_to_status()
    {
        $status = Status::findOrFail(Status::OPEN);
        $ticket = Ticket::factory(['status_id' => $status])->create();

        $this->assertEquals('Open', $ticket->status->name);
    }

    public function test_it_belongs_to_status_on_hold_reason()
    {
        $ticket = Ticket::factory(['on_hold_reason_id' => OnHoldReason::CALLER_RESPONSE])
            ->onHold()->create();

        $this->assertEquals('Caller Response', $ticket->onHoldReason->name);
    }

    public function test_it_belongs_to_group()
    {
        $group = Group::factory(['name' => 'LOCAL-6445-NEW-YORK'])->create();
        $ticket = Ticket::factory(['group_id' => $group])->create();

        $this->assertEquals('LOCAL-6445-NEW-YORK', $ticket->group->name);
    }

    public function test_it_belongs_to_item()
    {
        $category = Category::firstOrFail();
        $item = $category->items()->inRandomOrder()->first();
        $ticket = Ticket::factory(['category_id' => $category, 'item_id' => $item])->create();

        $this->assertEquals($item->name, $ticket->item->name);
    }

    function test_it_has_priority()
    {
        $ticket = Ticket::factory(['priority' => 4])->create();

        $this->assertEquals(4, $ticket->priority);
    }

    function test_it_has_description()
    {
        $ticket = Ticket::factory(['description' => 'Ticket Description'])->create();

        $this->assertEquals('Ticket Description', $ticket->description);
    }

    function test_sql_violation_thrown_when_higher_priority_than_predefined_is_assigned()
    {
        $this->expectException(QueryException::class);

        Ticket::factory(['priority' => count(Ticket::PRIORITIES) + 1])->create();
    }

    function test_it_has_correct_default_priority()
    {
        $ticket = new Ticket();

        $this->assertEquals(Ticket::DEFAULT_PRIORITY, $ticket->priority);
    }

    function test_it_has_correct_default_group(){
        $ticket = new Ticket();

        $this->assertEquals(Group::DEFAULT, $ticket->group->id);
    }

    function test_it_has_resolved_at_timestamp_null_when_status_changes_from_resolved_to_different_status(){
        $ticket = Ticket::factory()->create();
        $ticket->status_id = Status::RESOLVED;
        $ticket->save();

        $ticket->status_id = Status::IN_PROGRESS;
        $ticket->save();

        $this->assertEquals(null, $ticket->resolved_at);
    }

    function test_it_cannot_have_status_resolved_and_resolved_at_timestamp_null(){
        $ticket = Ticket::factory()->create();
        $ticket->status_id = Status::RESOLVED;
        $ticket->save();

        $this->assertNotEquals(null, $ticket->resolved_at);
    }

    function test_it_is_not_archived_when_resolved_status_does_not_exceed_archival_period(){
        $ticket = Ticket::factory()->create();
        $ticket->status_id = Status::RESOLVED;
        $ticket->save();

        $date = Carbon::now()->addDays(Ticket::ARCHIVE_AFTER_DAYS - 1);
        Carbon::setTestNow($date);

        $this->assertFalse($ticket->isArchived());
    }

    function test_it_is_archived_when_resolved_status_exceeds_archival_period(){
        $ticket = Ticket::factory()->create();
        $ticket->status_id = Status::RESOLVED;
        $ticket->save();

        $date = Carbon::now()->addDays(Ticket::ARCHIVE_AFTER_DAYS);
        Carbon::setTestNow($date);

        $this->assertTrue($ticket->isArchived());
    }

    public function test_exception_thrown_if_item_does_not_match_category()
    {
        // I'm detaching below models together, so they do not match
        $category = Category::findOrFail(Category::NETWORK);
        $item = Item::findOrFail(Item::APPLICATION_ERROR);
        $category->items()->detach($item);

        $this->withoutExceptionHandling();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Item cannot be assigned to Ticket if it does not match Category');

        Ticket::factory(['category_id' => $category, 'item_id' => $item])->create();
    }
}
