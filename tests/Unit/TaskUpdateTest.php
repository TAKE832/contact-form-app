<?php

namespace Tests\Unit;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class TaskUpdateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_it_will_not_causea_unique_error_when_tag_name_update(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Test',
        ]);

        $validator = Validator::make(
            ['name' => 'Test'],
            [
                'name' => [
                    'required',
                    Rule::unique('tags', 'name')->ignore($tag->id),
                ],
            ]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * 既に存在するタグ名に変更できない
     */
    public function test_will_validation_error_when_same_tag_name_update(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Test',
        ]);

        $validator = Validator::make(
            ['name' => 'Test'],
            [
                'name' => [
                    'required',
                    Rule::unique('tags', 'name'),
                ],
            ]
        );

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            'name',
            $validator->errors()->toArray(),
        );

        $this->assertEquals(
            'The name has already been taken.',
            $validator->errors()->first('name'),
        );
    }
}
