<?php
namespace Tests\Feature\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
class UserProfileTest extends TestCase
{
    use RefreshDatabase;
    public function test_mypage_displays()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);
    }
    public function test_user_profile_displays_all_information()
    {
        $user = User::factory()->create(['name' => 'テストユーザー', 'email_verified_at' => now()]);
        Profile::factory()->create(['user_id' => $user->id]);
        Item::factory()->create(['user_id' => $user->id, 'name' => '出品商品']);
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
    }
    public function test_guest_cannot_access_mypage()
    {
        $response = $this->get('/mypage');
        $response->assertRedirect('/login');
    }
}
