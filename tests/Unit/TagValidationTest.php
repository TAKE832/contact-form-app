<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TagValidationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * タグ作成時に名前が入力されていないとバリデーションエラーになる
     */
    public function test_tag_name_required_when_tag_create(): void
    {
        $request = new StoreTagRequest();

        $tag = [
            'name' => '',
        ];

        $validator = Validator::make(
            $tag,
            $request->rules(),
            $request->messages(),
        );

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'name',
            $validator->errors()->toArray(),
        );

        $this->assertEquals(
            'タグ名を入力してください。',
            $validator->errors()->first('name'),
        );
    }

    /**
     * タグ名は最大50文字しか入力できない
     */
    public function test_tag_name_max_50_valid(): void
    {
        $request = new StoreTagRequest();

        $tag = [
            'name' => str_repeat('あ', 50),
        ];

        $validator = Validator::make(
            $tag,
            $request->rules(),
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * タグ名は51文字だとバリデーションエラーになる
     */
    public function test_refuse_tag_name_number_of_character_51()
    {
        $request = new StoreTagRequest();

        $tag = [
            'name' => str_repeat('あ', 51),
        ];

        $validator = Validator::make(
            $tag,
            $request->rules(),
            $request->messages(),
        );

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'name',
            $validator->errors()->toArray(),
        );

        $this->assertEquals(
            'タグ名は50文字以内で入力してください。',
            $validator->errors()->first('name'),
        );
    }

    /**
     * 同じタグ名はバリデーションエラーになる
     */
    public function test_should_tag_name_unique(): void
    {
        $request = new StoreTagRequest();

        $tag = Tag::factory()->create([
            'name' => 'テスト',
        ]);

        $invalidTag = [
            'name' => 'テスト',
        ];

        $validator = Validator::make(
            $invalidTag,
            $request->rules(),
            $request->messages(),
        );

        $this->assertTrue($validator->fails());

        $this->assertDatabaseHas('tags', ['name' => 'テスト']);

        $this->assertArrayHasKey(
            'name',
            $validator->errors()->toArray(),
        );

        $this->assertEquals(
            'そのタグ名は既に使用されています。',
            $validator->errors()->first('name'),
        );
    }
}
