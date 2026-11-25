<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRelationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * カテゴリーから複数のお問い合わせを取得できる
     */
    public function test_category_has_many_contacts(): void
    {
        $category = Category::factory()->create([
            'content' => 'テスト',
        ]);

        $contacts = Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(3, $category->contacts);
    }
}
