<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン画面が表示できる
     */
    public function test_can_login_view(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    /**
     * 正しい認証情報でログインできる
     */
    public function test_admin_can_login_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('admin'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 間違ったパスワードではログインできない
     */
    public function test_admin_cannot_login_post_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@email.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 存在しないメールアドレスではログインできない
     */
    public function test_admin_cannot_login_post_does_not_exists_email(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * メールアドレスが空だとバリデーションエラーになる
     */
    public function test_admin_cannot_login_if_email_value_null(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが空だとバリデーションエラーになる
     */
    public function test_admin_cannot_login_if_password_value_null(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * ログアウトできる
     */
    public function test_admin_can_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'test@exampel.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * 認証済みユーザーはログインページにアクセスできない
     */
    public function test_admin_cannot_route_get_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('admin'));
    }
}
