<?php
namespace Tests\Feature\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
class AddressChangeTest extends TestCase
{
    use RefreshDatabase;
    public function test_address_edit_page_displays()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/address/edit');
        $response->assertStatus(200);
    }
    public function test_user_can_update_address_via_profile()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->post('/address/update', ['postal_code' => '123-4567', 'address' => '東京都渋谷区1-2-3', 'building' => 'テストビル101']);
        $response->assertStatus(302);
        $this->assertDatabaseHas('profiles', ['user_id' => $user->id, 'postal_code' => '123-4567']);
    }
    public function test_purchase_address_change_page_displays()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->get("/purchase/address/{$item->id}");
        $response->assertStatus(200);
    }
    public function test_user_can_update_address_during_purchase()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->post("/purchase/address/{$item->id}", ['postal_code' => '987-6543', 'address' => '大阪府大阪市', 'building' => '更新ビル']);
        $response->assertStatus(302);
    }
}
