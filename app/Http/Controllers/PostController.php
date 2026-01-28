<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // GET /posts
    public function index()
    {
        $posts = Post::with('user')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->paginate(20);

        return response()->json($posts);
    }

    // GET /posts/create
    public function create()
    {
        return "posts.create";
    }

    // POST /posts
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $data['user_id'] = Auth::id();

        $post = Post::create($data);

        return response()->json($post, 201);
    }

    // GET /posts/{post}
    public function show(Post $post)
    {
        if (!$post->isPublished()) {
            abort(404);
        }

        $post->load('user');

        return response()->json($post);
    }

    // GET /posts/{post}/edit
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return "posts.edit";
    }

    // PUT/PATCH /posts/{post}
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'published_at' => 'nullable|date',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    // DELETE /posts/{post}
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }
}
