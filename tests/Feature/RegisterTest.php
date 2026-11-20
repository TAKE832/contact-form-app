<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー登録画面が表示される
     */
    public function test_can_register_view(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    /**
     * ユーザーは正しい情報でユーザー登録ができる
     */
    public function test_user_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin'));
        $this->assertDatabaseHas('users', [
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * nameが空だとバリデーションエラーになる
     */
    public function test_user_cannot_register_if_name_value_null(): void
    {
        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * 名前が255文字以内だと登録できる
     */
    public function test_user_can_register_name_value_max_255(): void
    {
        $response = $this->post(route('register'), [
            'name' => str_repeat('あ', 255),
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin'));
        $this->assertDatabaseHas('users', [
            'name' => str_repeat('あ', 255),
            'email' => 'test@example.com',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * ユーザーは256文字だと登録できない
     */
    public function test_user_cannot_register_name_value_over_255(): void
    {
        $response = $this->post(route('register'), [
            'name' => str_repeat('あ', 256),
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertguest();
    }

    /**
     * メールアドレスが空だとバリデーションエラーになる
     */
    public function test_user_cannot_register_if_email_value_null(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test',
            'email' => '',
            'password' => 'test@example.com',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * メールアドレスが無効な型だとバリデーションエラーになる
     */
    public function test_user_cannot_register_if_email_value_invalid(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test',
            'email' => 'invalidEmail',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * メールアドレスが重複しているとバリデーションエラーになる
     */
    public function test_user_cannot_register_if_same_email_already_exists(): void
    {
        $user = User::factory()->create([
            'email' => 'unique@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('register'), [
            'name' => 'Test',
            'email' => 'unique@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 再確認用のパスワードが間違っているとバリデーションエラーになる
     */
    public function test_user_cannot_register_password_confirmation_is_wrong(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'worngpassword',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
