<?php
namespace Tests\Feature\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;
    public function test_payment_method_selection_page_loads()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");
        $response->assertStatus(200);
    }
    public function test_payment_form_displays()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/payment');
        $response->assertStatus(200);
    }
}
