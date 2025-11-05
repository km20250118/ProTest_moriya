<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
class ItemSearchTest extends TestCase
{
    use RefreshDatabase;
    public function test_search_by_item_name()
    {
        $user = User::factory()->create();
        Item::factory()->create(['user_id' => $user->id, 'name' => 'iPhone 15 Pro']);
        Item::factory()->create(['user_id' => $user->id, 'name' => 'MacBook Pro']);
        $response = $this->get('/item?keyword=iPhone');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro');
        $response->assertDontSee('MacBook Pro');
    }
    public function test_search_shows_matching_results()
    {
        $user = User::factory()->create();
        Item::factory()->create(['user_id' => $user->id, 'name' => 'テスト商品']);
        $response = $this->get('/item?keyword=テスト');
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }
}
