<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'email', 'password']);

        // upload profile image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $data['profile_image'] = $imageName;
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }

    /**
     * Login user (Sanctum)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = Auth::user();

        // Sanctum token
        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token
        ], 200);
    }

    /**
     * Get current logged-in user
     */
    public function me()
    {
        // ១. ចាប់យក User ដែលកំពុង Login
        $user = Auth::user();

        // ២. ឆែកមើលថា តើ User នោះមានរូបភាពឬអត់?
        if ($user && $user->profile_image) {
            
            // ៣. ឆែកមើលថា តើរូបនោះជា Link (http...) ស្រាប់ហើយឬនៅ?
            // បើមិនមែនជា Link ទេ (មានន័យថាជាឈ្មោះ file ដូចជា 123.jpg) ចាំថែម Domain
            if (!filter_var($user->profile_image, FILTER_VALIDATE_URL)) {
                
                // ៤. ប្រើ url() ដើម្បីភ្ជាប់ Domain + Folder + Image Name
                // លទ្ធផល៖ http://172.10.0.69:8000/images/123.jpg
                $user->profile_image = url('images/' . $user->profile_image);
            }
        }

        return response()->json($user, 200);
    }
    
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name']);

        // update image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);

            // delete old image
            if ($user->profile_image && file_exists(public_path('images/' . $user->profile_image))) {
                unlink(public_path('images/' . $user->profile_image));
            }

            $data['profile_image'] = $imageName;
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Delete user account
     */
    public function destroy()
    {
        $user = Auth::user();

        if ($user->profile_image && file_exists(public_path('images/' . $user->profile_image))) {
            unlink(public_path('images/' . $user->profile_image));
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
