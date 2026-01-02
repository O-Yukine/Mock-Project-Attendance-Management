<?php

namespace Tests\Feature\AdminAuth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_validation_email_is_empty()
    {
        $response = $this->post('/auth/admin-login', [
            'email' => '',
            'password' => 'password'
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください',]);
    }

    public function test_admin_validation_password_is_empty()
    {
        $response = $this->post('/auth/admin-login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください',]);
    }

    public function test_admin_login_validation_information_notmatch()
    {
        Admin::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->from('/auth/admin-login')->post('/auth/admin-login', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $this->assertGuest('admin');

        $response->assertSessionHasErrors(['login_error' => 'ログイン情報が登録されていません',]);
    }
}
