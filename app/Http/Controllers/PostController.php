<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('user')->get();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'posts' => $posts
        ]);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $data = $this->validate($request , [
                'title' => "required|between:2,200",
                'description' => "required"
            ]);
        }catch (\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

        auth()->user()->posts()->create(array_merge($data , ['slug' => Str::slug($request->title)]));

        return response()->json([
            'status' => true,
            'message' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'post' => $post
            ]);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        if(auth()->id() === $post->user_id)
        {
            try {
                $data = $this->validate($request , [
                    'title' => "required|between:2,200",
                    'description' => "required"
                ]);
            }catch (\Exception $e)
            {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }

            auth()->user()->posts()->update(array_merge($data , ['slug' => Str::slug($request->title)]));

            return response()->json([
                'status' => true,
                'message' => 'success',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (auth()->id() === $post->user_id)
        {
            $post->delete();

            return response()->json([
                'status' => true,
                'message' => 'success',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed',
        ]);
    }
}
