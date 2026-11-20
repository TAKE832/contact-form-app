<?php

namespace Tests\Feature;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * カテゴリーとタグの情報が入ったお問い合わせ入力フォームが表示される
     */
    public function test_contact_input_form_shows_categories_and_tags(): void
    {
        $categories = Category::factory()->count(5)->create();

        $tags = Tag::factory()->count(5)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('categories', function ($categories) {
            return $categories->count() === 5;
        });
        $response->assertViewHas('tags', function ($tags) {
            return $tags->count() === 5;
        });
    }

    /**
     * 入力フォームが正しく入力されている状態で送信すると確認画面に入力値がが表示される
     */
    public function test_contact_confirm_page_displays_submitted_value(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertViewIs('contact.confirm');
        $response->assertSeeText([
            'Test',
            'San',
            'test@email.com',
            '09011112222',
            '東京都浅草',
            '浅草マンション',
            $category->content,
            'テストの提出方法について',
        ]);
    }

    /**
     * first_nameが空の状態で送信した場合、エラーが発生する
     */
    public function test_return_the_validation_error_if_submit_first_name_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => '',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('first_name');
    }

    /**
     * last_nameが空の状態で送信した時バリデーションエラーが発生する
     */
    public function test_return_the_validation_error_if_submit_last_name_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => '',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('last_name');
    }

    /**
     * genderが空の状態で送信した時バリデーションエラーが発生する
     */
    public function test_return_the_validation_error_if_submit_gender_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => '',
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('gender');
    }

    /**
     * emailが空の状態で送信した時バリデーションエラーが発生する
     */
    public function test_return_the_validation_error_if_submit_email_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => 1,
            'email' => '',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');
    }

    /**
     * telが空の状態で送信した時バリデーションエラーが発生する
     */
    public function test_return_the_validation_error_if_submit_tel_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('tel');
    }

    /**
     * addressが空の状態で送信した時バリデーションエラーが発生する
     */
    public function test_return_the_validation_error_if_submit_address_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('address');
    }

    /**
     * category_idが空の状態で送信した時バリデーションエラーが発生する
     */
    public function test_return_the_validation_error_if_submit_category_id_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => '',
            'detail' => 'テストの提出方法について',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('category_id');
    }

    /**
     * detailが空の状態で送信した時バリデーションエラーが発生する
     */
    public function test_return_the_validation_error_if_submit_detail_value_is_null(): void
    {
        $category = Category::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => '',
        ];

        $response = $this->post(route('contacts.confirm'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('detail');
    }

    /**
     * お問い合わせが確認画面から送信されたとき、レコードと付与されたタグがそれぞれのテーブルに保存される
     */
    public function test_validated_contact_with_tag_are_save_in_suitable_table(): void
    {
        $category = Category::factory()->create();

        $tag = Tag::factory()->create();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの送信方法について',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post(route('contacts.store'), $data);

        $response->assertRedirect(route('thanks'));
        $this->assertDatabaseHas('contacts', [
            'first_name' => 'Test',
        ]);

        $contact = Contact::where(
            'first_name',
            'Test'
        )->first();

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    /**
     * 完了画面から送られるデータが不正な場合、バリデーションエラーが発生する
     */
    public function test_return_validation_error_if_submitted_invalid_data_in_route_contacts_confirm(): void
    {
        $category = Category::factory()->create();


        $data = [
            'first_name' => '',
            'last_name' => 'San',
            'gender' => 1,
            'email' => 'test@email.com',
            'tel' => '09011112222',
            'address' => '東京都浅草',
            'building' => '浅草マンション',
            'category_id' => $category->id,
            'detail' => 'テストの送信方法について',
        ];

        $response = $this->post(route('contacts.store'), $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('first_name');
    }
}
