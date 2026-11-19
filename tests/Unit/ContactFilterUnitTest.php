<?php

namespace Tests\Unit;

use App\Http\Requests\IndexQueryRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactFilterUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * キーワードフィルタにバリデーションが適用されている
     */
    public function test_validation_works_with_keyword_filter(): void
    {
        $request = new IndexQueryRequest();

        $this->assertEquals(
            'nullable|string|max:255',
            $request->rules()['keyword']
        );
    }

    /**
     * 性別フィルタにバリデーションが適用されている
     */
    public function test_validation_works_with_gender_filter(): void
    {
        $request = new IndexQueryRequest();

        $this->assertEquals(
            'nullable|integer|in:0,1,2,3',
            $request->rules()['gender'],
        );
    }

    /**
     * 不正な性別値を拒否する
     */
    public function test_refusal_invalid_gender_value(): void
    {
        $request = new IndexQueryRequest();

        $data = [
            'gender' => 999,
        ];

        $validator = Validator::make(
            $data,
            $request->rules(),
        );

        $this->assertTrue($validator->fails());

        $this->assertArrayHaskey(
            'gender',
            $validator->errors()->toArray()
        );
    }

    /**
     * カテゴリフィルタにバリデーションが適用されている
     */
    public function test_validation_works_with_category_filter(): void
    {
        $request = new IndexQueryRequest();

        $this->assertEquals(
            'nullable|integer|exists:categories,id',
            $request->rules()['category_id'],
        );
    }

    /** 
     * 日付フィルタにバリデーションが適用されている
     */
    public function test_validation_works_with_date_filter(): void
    {
        $request = new IndexQueryRequest();

        $this->assertEquals(
            'nullable|date',
            $request->rules()['date'],
        );
    }
}