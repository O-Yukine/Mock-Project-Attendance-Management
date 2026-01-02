<?php

namespace Tests\Feature\UserAuth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_validation_name_is_empty()
    {
        $response = $this->post('/auth/register', [
            'name' => '',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'

        ]);
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください',]);
    }
    public function test_register_validation_email_is_empty()
    {
        $response = $this->post('/auth/register', [
            'name' => 'test',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password'

        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください',]);
    }
    public function test_register_validation_password_is_empty()
    {
        $response = $this->post('/auth/register', [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => '',
            'password_confirmation' => ''

        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください',]);
    }
    public function test_register_validation_password_is_less_than_eight()
    {
        $response = $this->post('/auth/register', [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'pass',
            'password_confirmation' => 'pass'

        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください',]);
    }
    public function test_register_validation_password_not_match()
    {
        $response = $this->post('/auth/register', [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'passworld'
        ]);
        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません',]);
    }
}
