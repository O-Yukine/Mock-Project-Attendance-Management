<?php

namespace Tests\Feature\UserAuth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_validation_email_is_empty()
    {
        $response = $this->post('/auth/login', [
            'email' => '',
            'password' => 'password'
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください',]);
    }

    public function test_user_validation_password_is_empty()
    {
        $response = $this->post('/auth/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください',]);
    }

    public function test_user_login_validation_information_notmatch()
    {
        User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->post('/auth/login', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors(['login_error' => 'ログイン情報が登録されていません',]);
    }
}
