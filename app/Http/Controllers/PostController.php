<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Auth::user()->posts()->with('tags')->orderByDesc('pinned')->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image',
            'tags' => 'required|array|min:1',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['title', 'body', 'pinned']);
        $data['user_id'] = Auth::id();
        $data['cover_image'] = $request->file('cover_image')->store('cover_images');

        $post = Post::create($data);
        $post->tags()->attach($request->tags);





        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    public function show($id)
    {
        $post = Auth::user()->posts()->with('tags')->findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Auth::user()->posts()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'cover_image' => 'nullable|image',
            'tags' => 'sometimes|array|min:1',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['title', 'body', 'pinned']);
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('cover_images');
        }

        $post->update($data);
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Auth::user()->posts()->findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post softly deleted.']);
    }

        public function viewDeleted()
        {

            $deletedPosts = Auth::user()->posts()->onlyTrashed()->with('tags')->get();

            if ($deletedPosts->isEmpty()) {
                return response()->json(['message' => 'No deleted posts found.'], 404);
            }

            return response()->json($deletedPosts);
        }

    public function restore($id)
    {
        $post = Auth::user()->posts()->onlyTrashed()->findOrFail($id);
        $post->restore();

        return response()->json(['message' => 'Post restored successfully.']);
    }
}
