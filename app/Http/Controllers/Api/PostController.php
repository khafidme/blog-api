<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
# Import the Post model.
use App\Models\Post;
# Import the PostResource.
use App\Http\Resources\PostResource;
# Import Facades Validator.
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * index
     * @return void
     */
    public function index() {
        # Get all posts.
        $posts = Post::latest()->paginate(5);

        # Return a collection of posts as a resource.
        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
     * Store a newly created resource in storage.
     * @param mixed $request
     * @return void
     */
    public function store(Request $request) {
        # Define validation rule.
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        # if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        # Upload image to storage.
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        # Create a post.
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        # Return a newly created post as a resource.
        return new PostResource(true, 'Data Post Berhasil Disimpan', $post);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return void
     */
    public function show($id) {
        # Get a single post.
        $post = Post::findOrFail($id);

        # Return a single post as a resource.
        return new PostResource(true, 'Detail Data Post', $post);
    }

    /**
     * Update the specified resource in storage.
     * @param mixed $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id) {
        # Validate the request.
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);

        # if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        # Get a single post by id.
        $post = Post::findOrFail($id);

        # Check if the request has image.
        if ($request->hasfile('image')) {
            # Upload image to storage.
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            # Delete old image.
            Storage::delete('public/posts/' .basename($post->image));

            # Update a post.
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else {
            # Update post without image.
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }        

        # Return an updated post as a resource.
        return new PostResource(true, 'Data Post Berhasil Diupdate', $post);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return void
     */
    public function destroy($id) {
        # Get a single post.
        $post = Post::findOrFail($id);

        # Delete a post.
        $post->delete();

        # Return a deleted post as a resource.
        return new PostResource(true, 'Data Post Berhasil Dihapus', $post);
    }
}