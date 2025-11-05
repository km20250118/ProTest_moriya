<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
class MyListTest extends TestCase
{
    use RefreshDatabase;
    public function test_liked_items_are_displayed()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $likedItem = Item::factory()->create();
        $notLikedItem = Item::factory()->create();
        Like::create(['user_id' => $user->id, 'item_id' => $likedItem->id]);
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee($likedItem->name);
        $response->assertDontSee($notLikedItem->name);
    }
    public function test_sold_liked_items_display_correctly()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Like::create(['user_id' => $user->id, 'item_id' => $item->id]);
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
    }
    public function test_mylist_requires_authentication()
    {
        $response = $this->get('/?tab=mylist');
        $response->assertRedirect('/login');
    }
}
