<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // បង្កើត Comment ថ្មីសម្រាប់ Post ណាមួយ
    public function store(Request $request, $post_id)
    {
        $post = Post::find($post_id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'text' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post_id,
            'text' => $request->text
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Comment added',
            'comment' => $comment
        ], 201);
    }

    // កែប្រែ Comment
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['text' => 'required|string']);

        $comment->update(['text' => $request->text]);

        return response()->json([
            'status' => true,
            'message' => 'Comment updated',
            'comment' => $comment
        ], 200);
    }

    // លុប Comment
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Comment deleted'
        ], 200);
    }
}