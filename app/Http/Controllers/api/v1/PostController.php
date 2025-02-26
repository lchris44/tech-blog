<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    /**
     * Fetch a paginated list of posts, optionally filtered by tags.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PostRequest $request)
    {
        // Start with a base query for all posts
        $query = Post::query();

        // Filter by tags if the 'tags' parameter is provided
        if ($request->has('tags')) {
            // Fetch tags that match any of the provided tag names (in English or Italian)
            $tags = Tag::where(function ($q) use ($request) {
                foreach ($request->tags as $tag) {
                    $q->orWhere('name->en', $tag)
                        ->orWhere('name->it', $tag);
                }
            })->get();

            // If matching tags are found, filter posts that have any of these tags
            if ($tags->isNotEmpty()) {
                $query->whereHas('tags', function ($q) use ($tags) {
                    $q->whereIn('tags.id', $tags->pluck('id'));
                });
            }
        }

        // Generate a unique cache key based on the tags and page number
        $cacheKey = 'posts_'.implode('_', $request->tags ?? ['all']).'_page_'.$request->get('page', 1);

        // Paginate the results and cache them for 60 seconds
        $posts = Cache::remember($cacheKey, 60, function () use ($query) {
            return $query->with('tags')->paginate(10);
        });

        // Return the paginated posts as a collection of PostResource
        return PostResource::collection($posts);
    }
}
