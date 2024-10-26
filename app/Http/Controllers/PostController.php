<?php

namespace App\Http\Controllers;



use App\common\ApiResponse;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Auth::user()->posts()->with('tags')->orderByDesc('pinned')->get();
        return PostResource::collection($posts);
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
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = public_path('uploads/cover_images');
            $file->move($path, $filename);
            $data['cover_image'] = $filename;
        }


        $post = Post::create($data);
        $post->tags()->attach($request->tags);


        return ApiResponse::sendResponse(Response::HTTP_OK, 'Post created successfully', new PostResource($post));


    }

    public function show($id)
    {
        $post = Auth::user()->posts()->with('tags')->findOrFail($id);
      return ApiResponse::sendResponse(Response::HTTP_OK, 'Post retrieved successfully', new PostResource($post));
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


        if ($request->has('title')) {
            $post->title = $request->input('title');
        }
        if ($request->has('body')) {
            $post->body = $request->input('body');
        }


        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }


        if ($request->hasFile('cover_image')) {

            if ($post->cover_image) {
                $oldPhotoPath = public_path('uploads/cover_images/' . $post->cover_image);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }


            $file = $request->file('cover_image');
            $photoName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/cover_images'), $photoName);
            $post->cover_image = $photoName;
        }

        $post->save();

        return ApiResponse::sendResponse(Response::HTTP_OK, 'Post Information', new PostResource($post));


    }

    public function destroy($id)
    {
        $post = Auth::user()->posts()->findOrFail($id);
        $post->delete();

        return ApiResponse::sendResponse(Response::HTTP_OK, 'Post deleted successfully');

    }

        public function viewDeleted()
        {

            $deletedPosts = Auth::user()->posts()->onlyTrashed()->with('tags')->get();

            if ($deletedPosts->isEmpty()) {
                return response()->json(['message' => 'No deleted posts found.'], 404);
            }

            return ApiResponse::sendResponse(Response::HTTP_OK, 'Deleted posts', $deletedPosts);
        }

    public function restore($id)
    {
        $post = Auth::user()->posts()->onlyTrashed()->findOrFail($id);
        $post->restore();


       return ApiResponse::sendResponse(Response::HTTP_OK, 'Post restored successfully');
    }
}
