<?php

namespace Resolver;

use App\Models\Group;
use App\Models\Resolver;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResolverTest extends TestCase
{
    use RefreshDatabase;

    function test_it_can_belong_to_multiple_groups()
    {
        $groupOne = Group::factory(['name' => 'Group 0'])->create();
        $groupTwo = Group::factory(['name' => 'Group 1'])->create();
        $resolver = Resolver::factory()->create();

        $resolver->groups()->attach($groupOne);
        $resolver->groups()->attach($groupTwo);

        $groups = $resolver->groups;

        for($i = 0; $i <= count($groups) - 1; $i++){
            $this->assertEquals('Group ' . $i, $groups[$i]->name);
        }
    }

    function test_only_one_resolver_can_be_assigned_to_ticket()
    {
        $ticket = Ticket::factory()->create();
        $resolverOne = Resolver::factory()->create();
        $resolverTwo = Resolver::factory()->create();

        $ticket->assign($resolverOne);

        $this->assertEquals($resolverOne, $ticket->resolver);

        $ticket->assign($resolverTwo);
        $this->assertEquals($resolverTwo, $ticket->resolver);
        $this->assertNotEquals($resolverOne, $ticket->resolver);
    }

    function test_only_resolver_with_permission_can_change_ticket_priority()
    {
        $resolverWithPermission = Resolver::factory(['can_change_priority' => true])->create();
        $resolverWithoutPermission = Resolver::factory()->create();

        $ticket = Ticket::factory([
            'resolver_id' => $resolverWithPermission,
            'priority' => 4,
        ])->create();

        $this->actingAs($resolverWithPermission);

        $result = $this->patch(route('tickets.setPriority', ['priority' => 2, 'id' => $ticket]));

        $result->assertRedirectToRoute('tickets.show', $ticket);

        $ticket = Ticket::findOrFail($ticket->id);

        $this->assertEquals(2, $ticket->priority);
    }
}
