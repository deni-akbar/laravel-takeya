<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Posts\StorePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    // GET /posts
    public function index()
    {
        $posts = Post::with('user')
            ->active()
            ->paginate(20);

        return PostResource::collection($posts);
    }

    // GET /posts/create
    public function create()
    {
        return response()->json('posts.create');
    }

    // POST /posts
    public function store(StorePostRequest $request)
    {
        $post = Post::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return new PostResource($post);
    }

    // GET /posts/{post}
    public function show(Post $post)
    {
        if (! $post->isPublished()) {
            abort(404);
        }

        $post->load('user');

        return new PostResource($post);
    }

    // GET /posts/{post}/edit
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return response()->json('posts.edit');
    }

    // PUT/PATCH /posts/{post}
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return new PostResource($post);
    }

    // DELETE /posts/{post}
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Deleted',
        ], 200);
    }
}
