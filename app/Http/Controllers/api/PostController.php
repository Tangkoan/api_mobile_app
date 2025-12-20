<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
{
    // 1. ទាញយក Data
    $posts = Post::with('user')->latest()->paginate(10);

    // 2. Loop ដើម្បីកែទិន្នន័យ
    foreach ($posts as $post) {
        
        // --- កែសម្រួល Image Post ---
        // ឆែកមើល៖ បើមានរូប AND រូបនោះមិនទាន់មានពាក្យ 'http' នៅពីមុខ (ដើម្បីកុំអោយជាន់គ្នា)
        if ($post->image && !str_starts_with($post->image, 'http')) {
            // យោងតាមរូបភាពដែលអ្នកផ្ញើមក Folder ឈ្មោះ "images/posts" (មិនមែន uploads ទេ)
            $post->image = asset('images/posts/' . $post->image);
        }

        // --- កែសម្រួល Profile Image User ---
        if ($post->user && $post->user->profile_image && !str_starts_with($post->user->profile_image, 'http')) {
            // យោងតាមរូបភាព Folder គួរតែនៅក្នុង "images" ឬ "images/profiles" (សូមដាក់អោយត្រូវឈ្មោះ folder ពិត)
            // ឧទាហរណ៍៖ ខ្ញុំដាក់ asset('images/...') 
            $post->user->profile_image = asset('images/' . $post->user->profile_image);
        }

        // --- Logic រាប់ Like/Comment ---
        $post->likesCount = $post->likes->count();
        $post->commentsCount = $post->comments->count();
        $post->isLiked = $post->likes->contains('user_id', Auth::id());
    }

    return response()->json([
        'status' => true,
        'posts' => $posts
    ]);
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