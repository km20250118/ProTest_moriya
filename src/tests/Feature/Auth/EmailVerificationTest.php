<?php
namespace Tests\Feature\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_register()
    {
        Notification::fake();
        $response = $this->post('/register', ['name' => 'テストユーザー', 'email' => 'test@example.com', 'password' => 'password123', 'password_confirmation' => 'password123']);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }
    public function test_email_verification_notice_page_displays()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        session()->put('unauthenticated_user', $user);
        $response = $this->get('/email/verify');
        $response->assertStatus(200);
    }
    public function test_verification_email_is_sent()
    {
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => null]);
        session()->put('unauthenticated_user', $user);
        $response = $this->post('/email/verification-notification');
        $response->assertStatus(302);
        Notification::assertSentTo($user, VerifyEmail::class);
    }
    public function test_user_can_verify_email()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        session()->put('unauthenticated_user', $user);
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id' => $user->id, 'hash' => sha1($user->email)]);
        $response = $this->get($verificationUrl);
        $response->assertRedirect('/mypage/profile');
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }
}
