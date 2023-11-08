<?php


namespace Tests\Feature\Ticket;

use App\Livewire\TicketCreateForm;
use App\Models\Category;
use App\Models\Item;
use App\Models\Ticket;
use App\Models\TicketConfig;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;
    function test_it_redirects_guests_to_login_page()
    {
        $response = $this->get(route('tickets.create'));

        $response->assertRedirectToRoute('login');
    }

    function test_it_loads_to_auth_users()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get(route('tickets.create'));

        $response->assertSuccessful();
        $response->assertSee('Create Incident');
    }

    /**
     * @dataProvider invalidCategories
     */
    function test_it_fails_validation_with_invalid_category($value, $error){
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketCreateForm::class)
            ->set('category', $value)
            ->call('create')
            ->assertHasErrors(['category' => $error]);
    }

    /**
     * @dataProvider invalidItems
     */
    function test_it_fails_validation_with_invalid_item($value, $error){
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketCreateForm::class)
            ->set('item', $value)
            ->call('create')
            ->assertHasErrors(['item' => $error]);
    }

    /**
     * @dataProvider invalidDescription
     */
    function test_it_fails_validation_with_invalid_description($value, $error){
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketCreateForm::class)
            ->set('description', $value)
            ->call('create')
            ->assertHasErrors(['description' => $error]);
    }

    function test_user_can_set_category(){
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketCreateForm::class)
            ->set('category', Category::EMAIL)
            ->call('create')
            ->assertHasNoErrors(['category' => 'required']);
    }

    function test_user_can_set_item(){
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketCreateForm::class)
            ->set('item', Item::ISSUE)
            ->call('create')
            ->assertHasNoErrors(['item' => 'required']);
    }

    function test_user_can_set_description(){
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketCreateForm::class)
            ->set('description', Str::random(Ticket::MIN_DESCRIPTION_CHARS + 1))
            ->call('create')
            ->assertHasNoErrors(['description' => 'required']);
    }

    static function invalidCategories(){
        return [
            [Category::count() + 1, 'max'],
            [0, 'min'],
            ['ASAP', 'numeric'],
            ['', 'required'],
        ];
    }

    static function invalidItems(){
        return [
            ['', 'required'],
            [Item::count() + 1, 'max'],
            ['ASAP', 'numeric'],
            [0, 'min'],
        ];
    }

    static function invalidDescription(){
        return [
            ['', 'required'],
            [Str::random(Ticket::MIN_DESCRIPTION_CHARS - 1), 'min'],
            [Str::random(Ticket::MAX_DESCRIPTION_CHARS + 1), 'max'],
        ];
    }
}

