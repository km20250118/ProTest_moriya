<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_validation_name_required()
    {
        Mail::fake();
        
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }

    public function test_register_validation_email_required()
    {
        Mail::fake();
        
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_validation_password_required()
    {
        Mail::fake();
        
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_validation_password_must_be_string()
    {
        Mail::fake();
        
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 12345678,
            'password_confirmation' => 12345678,
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_validation_password_confirmation_mismatch()
    {
        Mail::fake();
        
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different456',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_success_with_valid_data()
    {
        Mail::fake();
        
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'テスト太郎',
        ]);
    }
}
