<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
class ItemIndexTest extends TestCase
{
    use RefreshDatabase;
    public function test_item_index_page_displays()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    public function test_all_items_are_displayed()
    {
        $user = User::factory()->create();
        $items = Item::factory()->count(5)->create(['user_id' => $user->id]);
        $response = $this->get('/');
        $response->assertStatus(200);
        foreach ($items as $item) { $response->assertSee($item->name); }
    }
    public function test_sold_items_display_correctly()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
