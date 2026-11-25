<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerSearchTest extends TestCase
{
    use RefreshDatabase;

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
}
