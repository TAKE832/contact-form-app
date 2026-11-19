<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagRelationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 中間テーブルを介して、1つのタグが複数の問い合わせに紐付く
     */
    public function test_tag_belongs_to_many_contacts_through_an_pivot_table(): void
    {
        $category = Category::factory()->create();

        $contacts = Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $tag = Tag::factory()->create();

        $tag->contacts()->attach($contacts->pluck('id')->toArray());

        $this->assertCount(3, $tag->contacts);
    }
}
