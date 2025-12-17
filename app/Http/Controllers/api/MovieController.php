<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    /**
     * 👉 Get all movies
     */
    public function index(Request $request)
    {
        try {
            $movies = Movie::orderBy('id', 'desc')->get();

            $domain = $request->getSchemeAndHttpHost();

            $formattedData = [];
            foreach ($movies as $movie) {
                if (!empty($movie->image_url)) {
                    $movie->image_url = $domain . $movie->image_url;
                }
                if (!empty($movie->video_url)) {
                    $movie->video_url = $domain . $movie->video_url;
                }
                $formattedData[] = $movie;
            }

            return response()->json([
                'code' => 1,
                'msg'  => 'Success',
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 0,
                'msg'  => $e->getMessage()
            ]);
        }
    }

    /**
     * 👉 Create movie
     */
    public function store(Request $request)
    {
        try {
            // ✅ Simple validation (ThinkPHP style)
            if (!$request->title) {
                return response()->json([
                    'code' => 0,
                    'msg' => 'Title is required'
                ]);
            }

            // ✅ Check duplicate title
            if (Movie::where('title', $request->title)->exists()) {
                return response()->json([
                    'code' => 0,
                    'msg' => 'This movie already exists'
                ]);
            }

            $movie = Movie::create([
                'title' => $request->title,
                'overview' => $request->overview ?? '',
                'image_url' => $request->image_url ?? '',
                'video_url' => $request->video_url ?? '',
                'published_date' => $request->published_date ?? ''
            ]);

            // attach domain
            $domain = $request->getSchemeAndHttpHost();
            if (!empty($movie->image_url)) {
                $movie->image_url = $domain . $movie->image_url;
            }
            if (!empty($movie->video_url)) {
                $movie->video_url = $domain . $movie->video_url;
            }

            return response()->json([
                'code' => 1,
                'msg'  => 'Movie created successfully',
                'data' => $movie
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 0,
                'msg'  => $e->getMessage()
            ]);
        }
    }

    /**
     * 👉 Get movie by ID
     */
    public function show(Request $request)
    {
        try {
            $id = $request->id;

            if (!$id || !is_numeric($id)) {
                return response()->json([
                    'code' => 0,
                    'msg' => 'Invalid movie ID'
                ]);
            }

            $movie = Movie::find($id);

            if (!$movie) {
                return response()->json([
                    'code' => 0,
                    'msg' => 'Movie not found'
                ]);
            }

            $domain = $request->getSchemeAndHttpHost();
            if (!empty($movie->image_url)) {
                $movie->image_url = $domain . $movie->image_url;
            }
            if (!empty($movie->video_url)) {
                $movie->video_url = $domain . $movie->video_url;
            }

            return response()->json([
                'code' => 1,
                'msg'  => 'Success',
                'data' => $movie
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 0,
                'msg'  => $e->getMessage()
            ]);
        }
    }
}
