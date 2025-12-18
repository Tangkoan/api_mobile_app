<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    // ទាញយក Post ទាំងអស់ (រាប់ទាំង like និង comment)
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')
            ->withCount(['comments', 'likes']) // រាប់ចំនួន comment និង like
            ->with(['likes', 'comments']) // ទាញយកព័ត៌មាន likes និង comments (optional)
            // ->with('user') // បើមាន relationship ជាមួយ user
            ->get();

        return response()->json([
            'status' => true,
            'posts' => $posts
        ], 200);
    }

    // បង្កើត Post ថ្មី
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caption' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/posts'), $imageName);
        }

        $post = Post::create([
            'user_id' => Auth::id(), // ចាំបាច់ត្រូវមាន user_id ក្នុង posts migration
            'caption' => $request->caption,
            'image' => $imageName
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    // បង្ហាញ Post តែមួយ
    public function show($id)
    {
        $post = Post::withCount(['comments', 'likes'])->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json([
            'status' => true,
            'post' => $post
        ], 200);
    }

    // កែប្រែ Post (Update)
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // Check ownership (ការពារកុំឱ្យអ្នកផ្សេងកែ Post របស់យើង)
        if ($post->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'caption' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update logic
        $post->caption = $request->caption;

        if ($request->hasFile('image')) {
            // លុបរូបចាស់ចោល
            if ($post->image && file_exists(public_path('images/posts/' . $post->image))) {
                unlink(public_path('images/posts/' . $post->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/posts'), $imageName);
            $post->image = $imageName;
        }

        $post->save();

        return response()->json([
            'status' => true,
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }

    // លុប Post
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // Check ownership
        if ($post->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // លុបរូបភាពពី Folder
        if ($post->image && file_exists(public_path('images/posts/' . $post->image))) {
            unlink(public_path('images/posts/' . $post->image));
        }

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully'
        ], 200);
    }
}