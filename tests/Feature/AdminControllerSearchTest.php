<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 認証ユーザーは管理者画面にアクセスできる
     */
    public function test_admin_can_access_route_admin(): void
    {
        $user = User::factory()->create();

        $categories = Category::factory()->count(3)->create();

        $contacts = Contact::factory()->count(3)->create([
            'category_id' => Category::inRandomOrder()->first()->id,
        ]);

        $tags = Tag::factory()->count(3)->create(

        );

        $response = $this->actingAs($user)->get(route('admin'));

        $response->assertStatus(200);
        $response->assertViewHas('contacts');
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
    }

    /**
     * お問い合わせが7つずつ表示される
     */
    public function test_paginate_7_each_at_contacts(): void
    {
        $user = User::factory()->create();

        $category = category::factory()->create();

        $contacts = Contact::factory()->count(20)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get(route('admin'));

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 7
                && $contacts->total() === 20;
        });
    }
    /**
     * キーワードフィルタが管理者画面で動作する
     */
    public function test_work_keyword_filter_route_get_admin(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $SearchContact = Contact::factory()->create([
            'first_name' => 'Test',
            'category_id' => $category->id,
        ]);

        $contact = Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $contacts = Contact::all();

        $response = $this->actingAs($user)->get('/admin?keyword=Test');

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 1 && $contacts->every(function ($contact) {
                return $contact->first_name === 'Test';
            });
        });
    }

    /**
     * 性別フィルタが機能する
     */
    public function test_work_gender_filter_route_get_admin(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $searchContacts = Contact::factory()->count(3)->create([
            'gender' => '1',
            'category_id' => $category->id,
        ]);

        $ontherContacts = Contact::factory()->count(5)->create([
            'gender' => 2,
            'category_id' => $category->id,
        ]);

        $contacts = Contact::all();

        $response = $this->actingAs($user)->get('/admin?gender=1');

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 3 && $contacts->every(function ($contact) {
                return $contact->gender === 1;
            });
        });
    }

    /**
     * カテゴリーフィルタが動作する
     */
    public function test_work_category_filter_route_get_admin(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $searchcategory = Category::factory()->create([
            'id' => 2,
        ]);

        $searchContacts = Contact::factory()->count(3)->create([
            'category_id' => $searchcategory->id,
        ]);

        $ontherContacts = Contact::factory()->count(5)->create([
            'category_id' => $category->id,
        ]);

        $contacts = Contact::all();

        $response = $this->actingAs($user)->get('/admin?category_id=2');

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 3 && $contacts->every(function ($contact) {
                return $contact->category_id === 2;
            });
        });
    }

    /**
     * 日付フィルタ機能が動作する
     */
    public function test_work_date_filter_route_get_admin(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $searchContacts = Contact::factory()->count(3)->create([
            'created_at' => '2026-11-12',
            'category_id' => $category->id,
        ]);

        $ontherContacts = Contact::factory()->count(5)->create([
            'created_at' => '2026-10-29',
            'category_id' => $category->id,
        ]);

        $contacts = Contact::all();

        $response = $this->actingAs($user)->get('/admin?date=2026-11-12');

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 3 && $contacts->every(function ($contact) {
                return $contact->created_at->format('Y-m-d') === '2026-11-12';
            });
        });
    }

    /**
     * 指定したお問い合わせの詳細がカテゴリ情報付きで表示される
     */
    public function test_specified_contact_show_detail_page_with_category_route_get_admin_show(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create([
            'content' => 'Test',
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->get(route('contacts.show', $contact));

        $response->assertStatus(200);
        $response->assertViewHas('contact');
        $response->assertSeeText($category->content);
    }

    /**
     * 認証ユーザーはお問い合わせを消去することができる
     */
    public function test_admin_can_delete_contact_from_databese_route_delete_admin_contacts_contact(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->delete(route('contacts.destroy', $contact));

        $response->assertRedirect(route('admin'));
        $this->assertDatabaseMissing('contacts', [
            'contact_id' => $contact->id,
        ]);
    }
}