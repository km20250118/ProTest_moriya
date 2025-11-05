<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class ItemCreateTest extends TestCase
{
    use RefreshDatabase;
    public function test_sell_page_displays()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/sell');
        $response->assertStatus(200);
    }
    public function test_user_can_create_item_with_all_information()
    {
        Storage::fake('public');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        $file = UploadedFile::fake()->image('item.jpg');
        $response = $this->actingAs($user)->post('/sell', ['name' => 'テスト出品商品', 'brand' => 'テストブランド', 'price' => 10000, 'description' => '商品の説明文です。', 'condition_id' => $condition->id, 'categories' => [$category->id], 'image' => $file]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('items', ['user_id' => $user->id, 'name' => 'テスト出品商品']);
    }
    public function test_item_creation_validates_required_fields()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->post('/sell', ['name' => '', 'price' => '', 'description' => '']);
        $response->assertSessionHasErrors(['name', 'price', 'description']);
    }
    public function test_guest_cannot_access_sell_page()
    {
        $response = $this->get('/sell');
        $response->assertRedirect('/login');
    }
}
