<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 認証されていないユーザーは管理画面にアクセスできない
     */
    public function test_guest_user_cannot_access_admin_screen(): void
    {
        $response = $this->get(route('admin'));

        $response->assertRedirect(route('login'));
    }

    /**
     * 未認証ユーザーは問い合わせ詳細を取得できない
     */
    public function test_guest_user_cannot_view_contact_detail(): void
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->get(route('contacts.show', $contact));

        $response->assertRedirect(route('login'));
    }

    /**
     * 未認証ユーザーはお問い合わせを削除できない
     */
    public function test_guest_user_cannot_delete_contact(): void
    {
        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->delete(route('contacts.destroy', $contact));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);
    }
}
