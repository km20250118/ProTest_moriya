<?php
namespace Tests\Feature\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    public function test_purchase_page_displays()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 5000]);
        $response = $this->actingAs($buyer)->get("/purchase/{$item->id}");
        $response->assertStatus(200);
    }
    public function test_user_can_purchase_item()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create();
        Profile::factory()->create(['user_id' => $buyer->id]);
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 5000]);
        $response = $this->actingAs($buyer)->post("/purchase/{$item->id}", ['payment_method' => 'credit_card']);
        $response->assertStatus(302);
    }
    public function test_purchase_success_page_displays()
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $response = $this->actingAs($buyer)->get("/purchase/{$item->id}/success");
        $response->assertStatus(200);
    }
    public function test_address_change_page_displays()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->get("/purchase/address/{$item->id}");
        $response->assertStatus(200);
    }
}
