<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_posts_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/posts/create');

        $response->assertStatus(200);
        $response->assertSee('posts.create');
    }

    public function test_authenticated_user_can_store_post()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/posts', [
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_access_edit_post()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/posts/{$post->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('posts.edit');
    }

    public function test_authenticated_user_can_update_post()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->putJson("/posts/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);
    }

    public function test_authenticated_user_can_delete_post()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/posts/{$post->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }
}
