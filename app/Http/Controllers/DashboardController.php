<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with counts of posts and tags.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Get the total count of posts
        $posts_count = Post::count();

        // Get the total count of tags
        $tags_count = Tag::count();

        // Render the dashboard index page with the counts
        return Inertia::render('Dashboard/Index', [
            'posts_count' => $posts_count,
            'tags_count' => $tags_count,
        ]);
    }
}
