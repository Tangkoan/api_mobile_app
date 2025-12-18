<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // ប្រើសម្រាប់ Like ឬ Unlike (Toggle)
    public function likeOrUnlike($post_id)
    {
        $post = Post::find($post_id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $like = Like::where('post_id', $post_id)
                    ->where('user_id', Auth::id())
                    ->first();

        if ($like) {
            // បើមានរួចហើយ គឺលុបចោល (Unlike)
            $like->delete();
            return response()->json([
                'status' => true,
                'message' => 'Unliked'
            ], 200);
        } else {
            // បើមិនទាន់មាន គឺបង្កើតថ្មី (Like)
            Like::create([
                'post_id' => $post_id,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Liked'
            ], 201);
        }
    }
}