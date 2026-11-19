<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactRelationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1つのカテゴリーは1つのお問い合わせに属する
     */
    public function test_example(): void
    {
        $category = Category::factory()->create([
            'id' => 1,
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertTrue($category->contacts->contains($contact));
    }

    /**
     * 1つのお問い合わせは複数のタグを所有することができる
     */
    public function test_contact_belongs_to_many_tags(): void
    {
        $tag = Tag::factory()->count(3)->create();

        $category = Category::factory()->create();

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $contact->tags()->sync($tag->pluck('id'));

        $this->assertCount(3, $contact->tags);
    }
}
