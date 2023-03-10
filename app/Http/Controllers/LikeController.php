<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function likeOrUnlike($id) {
        $post = Post::find($id);
        
        if(!$post) {
            return response([
                'message' => 'post not found'
            ], 403);
        }

        $like = $post->likes()->where('user_id', Auth::user()->id)->first();

        if(!$like) {
            Like::create([
                'user_id' => Auth::user()->id,
                'post_id' => $post->id,
            ]);
            
            return response([
                'message' => 'post liked'
            ], 200);
        }

        $like->deleted();

        return response([
            'message' => 'post disliked'
        ], 200);
    }
}
