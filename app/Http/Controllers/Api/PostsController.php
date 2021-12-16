<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostsRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Posts;
use App\Models\User;

class PostsController extends Controller
{

    public function index()
    {
        $user = User::find(70)->posts;
        dd($user);

        $posts = Posts::when(request('posts_id'), function ($query) {

            return $query->where('id', request('posts_id'));
        })->when(request('title'), function ($query) {

            return $query->where('title', request('title'));
        })->paginate(2);

        return response()->json([
            'status' => true,
            'message' => __('List'),
            'data' => $posts
        ], 200);
    }

    public function store(PostsRequest $request)
    {

        $posts = Posts::create([
            'title' => $request->input('title'),
            'body' => $request->input('body'),
        ]);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('Created successfully!'),
            'data' => [
                'post' => $posts
            ]
        ], 201);
    }

    public function edit(UpdatePostRequest $request, $id)
    {
        $post = Posts::findorFail($id);

        $image = $request->avatar;
        if (!empty($image)) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploadFeatureImgPosts'), $imageName);
            $newAvatarUrl = "upload/" . $imageName;
        }

        $post->update([
            'title' => $request->get('title', $post->title),
            'body' => $request->get('body', $post->body),
            'feature_img' => empty($newAvatarUrl) ? $post->feature_img : $newAvatarUrl
        ]);

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('Update success'),
            'data' => [
                'post' => $post
            ]
        ]);
    }

    public function destroy($id)
    {
        $post = Posts::findorFail($id);

        $post->delete();

        return response([
            'status' => true,
            'locale' => app()->getLocale(),
            'message' => __('Delete user successfully!'),
        ], 200);
    }
}
