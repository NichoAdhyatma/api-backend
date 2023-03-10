<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at')->withCount('comments', 'likes')->with('likes', function($like) {
                return $like->where('user_id', Auth::user()->id)->select('id', 'user_id', 'post_id')->get();
            })->get(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attr = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'posts');

        Post::create([
            'user_id' =>  Auth::user()->id,
            'body' => $attr['body'],
            'image' => $image
        ]);

        return response([
            'message' => 'post created'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response(['post' => Post::find($id)], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response([
                'message' => 'post not found',
            ], 403);
        }

        if($post->user_id != Auth::user()->id) {
            return response([
                'message' => 'action not allowed',
            ], 403);
        }

        $attr = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $attr['body'],
        ]);

        return response([
            'message' => 'post updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response([
                'message' => 'post not found',
            ], 403);
        }

        if($post->user_id != Auth::user()->id) {
            return response([
                'message' => 'action not allowed',
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'post deleted'
        ], 200);
    }
}
