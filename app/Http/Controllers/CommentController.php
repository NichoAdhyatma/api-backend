<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function postComment(Request $request, String $id)
    {
        $post = Post::find($id);

        $post = Post::find($id);



        if (!$post) {
            return response([
                'message' => 'post not found',
            ], 403);
        }



        $attr = $request->validate([
            'body' => 'required|string'
        ]);


        Comment::create([
            'user_id' => Auth::user()->id,
            'body' => $attr['body'],
            'post_id' => $post->id
        ]);

        return response([
            'message' => 'comments posted',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        $post = Post::find($id);

        if (!$post) {
            return response([
                'message' => 'post not found',
            ], 403);
        }

        return response([
            'comments' => $post->comments(),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response([
                'message' => 'comment not found'
            ], 403);
        }

        if ($comment->user_id != Auth::user()->id) {
            return response([
                'message' => 'action not allowed',
            ], 403);
        }

        $attr = $request->validate([
            'body' => 'required|string'
        ]);

        $comment->update([
            'body' => $attr['body'],
        ]);


        return response([
            'message' => 'comments updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response([
                'message' => 'comment not found'
            ], 403);
        }

        if ($comment->user_id != Auth::user()->id) {
            return response([
                'message' => 'action not allowed',
            ], 403);
        }

        $comment->delete();

        return response([
            'message' => 'comments deleted',
        ], 200);
    }
}
