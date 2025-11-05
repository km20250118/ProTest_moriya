<?php
namespace Tests\Feature\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;
    public function test_profile_edit_page_displays()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/mypage/profile');
        $response->assertStatus(200);
    }
    public function test_profile_shows_current_user_information()
    {
        $user = User::factory()->create(['name' => '初期ユーザー名', 'email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/mypage/profile');
        $response->assertStatus(200);
        $response->assertSee('初期ユーザー名');
    }
    public function test_user_can_update_profile()
    {
        Storage::fake('public');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $file = UploadedFile::fake()->image('profile.jpg');
        $response = $this->actingAs($user)->post('/mypage/profile', ['name' => '更新後ユーザー名', 'postcode' => '987-6543', 'address' => '大阪府大阪市', 'building' => '更新ビル', 'image' => $file]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('profiles', ['user_id' => $user->id, 'postcode' => '987-6543']);
    }
}
