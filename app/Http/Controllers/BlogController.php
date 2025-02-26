<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * Display a paginated list of blog posts, optionally filtered by a tag.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Get the tag from the request query parameters
        $tag = $request->input('tag');

        // Query posts, filtering by tag if provided
        $posts = Post::when($tag, function ($query, $tag) {
            // Filter posts that have a tag with the specified name (English)
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->whereJsonContains('name->en', $tag);
            });
        })
            ->with(['tags']) // Eager load tags to avoid N+1 queries
            ->orderBy('created_at', 'desc') // Sort by creation date (newest first)
            ->paginate(9); // Paginate results (9 posts per page)

        // Fetch all tags for the sidebar or filter options
        $tags = Tag::all();

        // Render the blog index page with posts and tags
        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'tags' => $tags,
        ]);
    }

    /**
     * Display a single blog post with related posts and tags.
     *
     * @return \Inertia\Response
     */
    public function show(Post $post)
    {
        // Fetch all tags for the sidebar or filter options
        $tags = Tag::all();

        // Fetch related posts (excluding the current post)
        $relatedPosts = Post::where('id', '!=', $post->id)
            ->with(['user', 'tags']) // Eager load user and tags to avoid N+1 queries
            ->take(4) // Limit to 4 related posts
            ->get();

        // Render the blog post show page with the post, related posts, and tags
        return Inertia::render('Blog/Show', [
            'post' => $post->load('tags', 'user'), // Eager load tags and user for the current post
            'posts' => $relatedPosts,
            'tags' => $tags,
        ]);
    }
}
