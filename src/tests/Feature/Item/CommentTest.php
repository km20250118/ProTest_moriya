<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
class CommentTest extends TestCase
{
    use RefreshDatabase;
    public function test_authenticated_user_can_post_comment()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->post("/item/comment/{$item->id}", ['comment' => 'これは良い商品ですね！']);
        $response->assertStatus(302);
        $this->assertDatabaseHas('comments', ['user_id' => $user->id, 'item_id' => $item->id, 'comment' => 'これは良い商品ですね！']);
    }
    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();
        $response = $this->post("/item/comment/{$item->id}", ['comment' => 'コメント内容']);
        $response->assertRedirect('/login');
    }
    public function test_comment_validation_content_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->post("/item/comment/{$item->id}", ['comment' => '']);
        $response->assertSessionHasErrors('comment');
    }
    public function test_comment_validation_max_length()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $longComment = str_repeat('あ', 256);
        $response = $this->actingAs($user)->post("/item/comment/{$item->id}", ['comment' => $longComment]);
        $response->assertSessionHasErrors('comment');
    }
}
