<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
class ItemDetailTest extends TestCase
{
    use RefreshDatabase;
    public function test_item_detail_page_displays()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id, 'condition_id' => $condition->id]);
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);
    }
    public function test_item_detail_displays_all_information()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'ファッション']);
        $condition = Condition::factory()->create(['condition' => '新品、未使用']);
        $item = Item::factory()->create(['user_id' => $user->id, 'name' => 'テスト商品', 'brand' => 'テストブランド', 'price' => 5000, 'description' => '商品の説明文です', 'condition_id' => $condition->id]);
        $item->categories()->attach($category->id);
        Comment::factory()->count(3)->create(['item_id' => $item->id, 'user_id' => $user->id]);
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }
    public function test_item_categories_are_displayed_correctly()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $category1 = Category::factory()->create(['name' => 'メンズ']);
        $category2 = Category::factory()->create(['name' => 'アクセサリー']);
        $item = Item::factory()->create(['user_id' => $user->id, 'condition_id' => $condition->id]);
        $item->categories()->attach([$category1->id, $category2->id]);
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('メンズ');
        $response->assertSee('アクセサリー');
    }
}
