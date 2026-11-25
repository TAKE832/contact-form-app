<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 全ての入力項目が入った問い合わせがバリデーションを通過する
     */
    public function test_accept_all_item(): void
    {
        $request = new StoreContactRequest;

        $category = Category::factory()->create([
            'content' => 'テスト',
        ]);

        $contact = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09011112222',
            'address' => '東京都渋谷区',
            'building' => 'テストマンション111',
            'category_id' => $category->id,
            'detail' => 'これはテストです。',
        ];

        $validator = Validator::make(
            $contact,
            $request->rules()
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * 不正な電話番号の入力を拒否する
     */
    public function test_refusal_invalid_tel_number(): void
    {
        $request = new StoreContactRequest;

        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'tel' => '090112233',
            'category_id' => $category->id,
        ]);

        $validator = Validator::make(
            $contact->toArray(),
            $request->rules(),
        );

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'tel',
            $validator->errors()->toArray(),
        );
    }

    /**
     * タグ入力を受け付ける
     */
    public function test_accept_tag(): void
    {
        $request = new StoreContactRequest;

        $category = Category::factory()->create();

        $tag = Tag::factory()->create();

        $data = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contact = $data->toArray();
        $contact['tag_ids'] = [$tag->id];

        $validator = Validator::make(
            $contact,
            $request->rules(),
        );

        $this->assertTrue($validator->passes());

        $this->assertArrayHasKey('tag_ids', $contact);
    }
}
