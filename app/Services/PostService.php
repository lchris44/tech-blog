<?php

namespace App\Services;

use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostService
{
    /**
     * Create a new post.
     *
     * @return void
     *
     * @throws Exception
     */
    public function createPost(array $data, int $userId)
    {
        DB::transaction(function () use ($data, $userId) {
            $post = Post::create([
                ...$data,
                'user_id' => $userId,
            ]);

            // Attach tags (tags are mandatory, so no need to check if they exist)
            $tags = collect($data['tags'])->pluck('id')->toArray();
            $post->tags()->attach($tags);
        });
    }

    /**
     * Update an existing post.
     *
     * @return void
     *
     * @throws Exception
     */
    public function updatePost(Post $post, array $data)
    {
        DB::transaction(function () use ($post, $data) {
            $post->update($data);

            // Sync tags (tags are mandatory, so no need to check if they exist)
            $tags = collect($data['tags'])->pluck('id')->toArray();
            $post->tags()->sync($tags);
        });
    }

    /**
     * Delete a post.
     *
     * @return void
     *
     * @throws Exception
     */
    public function deletePost(Post $post)
    {
        DB::transaction(function () use ($post) {
            $post->delete();
        });
    }

    /**
     * Upload a cover image for a post.
     *
     * @param  mixed  $file
     * @return string
     *
     * @throws Exception
     */
    public function uploadCover(Post $post, $file)
    {
        if (! $file || ! $file->isValid()) {
            throw new Exception('Invalid file upload.');
        }

        // Generate a filename and store the file
        $extension = $file->getClientOriginalExtension();
        $filename = $post->id.'.'.$extension;
        $path = $file->storeAs('uploads/posts', $filename, 'public');

        // Save the file URL in the post model
        $post->cover = Storage::disk('public')->url($path);
        $post->save();

        return $post->cover;
    }

    /**
     * Remove the cover image for a post.
     *
     * @return void
     *
     * @throws Exception
     */
    public function removeCover(Post $post)
    {
        // Extract the path from the URL
        $urlPath = parse_url($post->cover, PHP_URL_PATH); // Returns "/storage/uploads/posts/20.jpeg"

        // Remove the "/storage" prefix
        $path = str_replace('/storage', '', $urlPath); // Returns "/uploads/posts/20.jpeg"

        // Remove leading/trailing slashes
        $path = trim($path, '/'); // Returns "uploads/posts/20.jpeg"

        // Clear the cover image URL in the post model
        $post->cover = null;
        $post->save();

        // Delete the file from storage
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
