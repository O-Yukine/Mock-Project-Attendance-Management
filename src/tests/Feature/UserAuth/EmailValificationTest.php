<?php

namespace Tests\Feature\UserAuth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomVerifyEmail;
use App\Models\User;

class EmailValificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_verification_email()
    {
        Notification::fake();

        $this->post('/auth/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, CustomVerifyEmail::class);
    }

    public function test_user_can_verify_email_via_link()
    {
        Notification::fake();

        $this->post('/auth/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, CustomVerifyEmail::class, function ($notification, $channels) use ($user, &$verificationUrl) {

            $verificationUrl = $notification->toMail($user)->actionUrl;
            return true;
        });

        $this->assertNull($user->email_verified_at);


        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_it_redirects_to_attendance_list_after_verify()
    {
        Notification::fake();

        $this->post('/auth/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, CustomVerifyEmail::class, function ($notification, $channels) use ($user, &$verificationUrl) {

            $verificationUrl = $notification->toMail($user)->actionUrl;
            return true;
        });

        $this->assertNull($user->email_verified_at);

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');
    }
}
