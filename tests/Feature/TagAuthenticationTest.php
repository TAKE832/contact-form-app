<?php

namespace Tests\Feature;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未認証ユーザーはタグを作成できない
     */
    public function test_guest_user_cannot_create_tag(): void
    {
        $response = $this->post(route('tags.store'), [
            'name' => 'Guest',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('tags', [
            'name' => 'Guest',
        ]);
    }

    /**
     * 未認証ユーザーはタグ編集画面にアクセスできない
     */
    public function test_guest_user_cannot_access_route_get_tags_tag_edit(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get(route('tags.edit', $tag));

        $response->assertRedirect(route('login'));
    }

    /**
     * 未認証ユーザーはタグを更新できない
     */
    public function test_guest_user_cannot_update_tag(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Test',
        ]);

        $response = $this->put(route('tags.update', $tag), [
            'name' => 'Guest',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Test',
        ]);
    }

    /**
     * 未認証ユーザーはタグを削除できない
     */
    public function test_guest_user_cannot_delete_tag(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Test',
        ]);

        $response = $this->delete(route('tags.destroy', $tag));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Test',
        ]);
    }
}
