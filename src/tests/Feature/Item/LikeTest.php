<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
class LikeTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_like_item()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->post("/item/like/{$item->id}");
        $response->assertStatus(302);
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'item_id' => $item->id]);
    }
    public function test_user_can_unlike_item()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Like::create(['user_id' => $user->id, 'item_id' => $item->id]);
        $response = $this->actingAs($user)->post("/item/unlike/{$item->id}");
        $response->assertStatus(302);
        $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'item_id' => $item->id]);
    }
    public function test_guest_cannot_like_item()
    {
        $item = Item::factory()->create();
        $response = $this->post("/item/like/{$item->id}");
        $response->assertRedirect('/login');
    }
    public function test_like_count_increases_and_decreases()
    {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $this->actingAs($user1)->post("/item/like/{$item->id}");
        $this->assertEquals(1, $item->likes()->count());
        $this->actingAs($user2)->post("/item/like/{$item->id}");
        $this->assertEquals(2, $item->likes()->count());
        $this->actingAs($user1)->post("/item/unlike/{$item->id}");
        $this->assertEquals(1, $item->likes()->count());
    }
}
