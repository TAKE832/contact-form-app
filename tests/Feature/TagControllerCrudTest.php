<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerCrudTest extends TestCase
{
    use RefreshDatabase;
    /**
     * タグを追加できる
     */
    public function test_admin_can_access_route_get_tags_store(): void
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Test',
        ];

        $response = $this->actingAs($user)->post(route('tags.store'), $data);

        $response->assertRedirect(route('admin'));
        $this->assertDatabaseHas('tags', [
            'name' => 'Test',
        ]);
    }

    /**
     * 管理者はタグ編集ページにアクセスできる
     */
    public function test_admin_can_access_route_tags_tag_edit(): void
    {
        $user = User::factory()->create();

        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->get(route('tags.edit', $tag));

        $response->assertStatus(200);
        $response->assertViewHas('tag');
    }

    /**
     * 管理者はタグを更新できる
     */
    public function test_admin_can_update_tag_route_put_tags_tag(): void
    {
        $user = User::factory()->create();

        $tag = Tag::factory()->create([
            'name' => 'Tag',
        ]);

        $response = $this->actingAs($user)->put(route('tags.update', $tag), [
            'name' => 'UpdateTag',
        ]);

        $response->assertRedirect(route('admin'));
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'UpdateTag',
        ]);
    }

    /**
     * 管理者はタグを削除できる
     */
    public function test_admin_can_destroy_tag_route_delete_tags_tag(): void
    {
        $user = User::factory()->create();

        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->delete(route('tags.destroy', $tag));

        $response->assertRedirect(route('admin'));
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}