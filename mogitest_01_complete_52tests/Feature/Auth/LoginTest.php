<?php
namespace Tests\Feature\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class LoginTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_validation_email_required()
    {
        $response = $this->post('/login', ['email' => '', 'password' => 'password123']);
        $response->assertSessionHasErrors('email');
    }
    public function test_login_validation_password_required()
    {
        $response = $this->post('/login', ['email' => 'test@example.com', 'password' => '']);
        $response->assertSessionHasErrors('password');
    }
    public function test_login_fails_with_invalid_credentials()
    {
        User::factory()->create(['email' => 'test@example.com', 'password' => Hash::make('correct'), 'email_verified_at' => now()]);
        $response = $this->post('/login', ['email' => 'test@example.com', 'password' => 'wrong']);
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    public function test_login_success_with_valid_credentials()
    {
        User::factory()->create(['email' => 'test@example.com', 'password' => Hash::make('password123'), 'email_verified_at' => now()]);
        $response = $this->post('/login', ['email' => 'test@example.com', 'password' => 'password123']);
        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }
}
