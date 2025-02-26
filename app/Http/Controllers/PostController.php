<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Tag;
use App\Services\Datatable\DatatableService;
use App\Services\PostService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    protected $postService;

    /**
     * Constructor to inject PostService.
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a paginated list of posts.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Query posts with user and tags relationships
        $query = Post::query()->with(['user', 'tags']);

        // Apply default ordering if no datatable parameters are provided
        if (! $request->has('dt_params')) {
            $query->orderBy('id', 'desc');
        }

        // Use Datatables service to format the response
        $posts = DatatableService::of($query)->make();

        // Render the posts index page with the payload
        return Inertia::render('Dashboard/Posts/Index', [
            'payload' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        // Initialize a new post with default values
        $post = new Post;

        $post->tags = [];

        // Fetch all tags for the form
        $tags = Tag::select('name', 'id')->get();

        // Render the edit modal with the post and tags
        return Inertia::modal('Dashboard/Posts/Edit', [
            'post' => $post,
            'tags' => $tags,
        ])
            ->baseRoute('posts.index')
            ->refreshBackdrop();
    }

    /**
     * Store a newly created post in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostRequest $request)
    {
        try {
            // Delegate post creation to the service
            $this->postService->createPost($request->validated(), auth()->id());

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Post Created!');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified post.
     *
     * @return \Inertia\Response
     */
    public function edit(Post $post)
    {
        // Fetch all tags for the form
        $tags = Tag::select('name', 'id')->get();

        // Render the edit modal with the post and tags
        return Inertia::modal('Dashboard/Posts/Edit', [
            'post' => $post->load('tags'),
            'tags' => $tags,
        ])
            ->baseRoute('posts.index')
            ->refreshBackdrop();
    }

    /**
     * Update the specified post in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PostRequest $request, Post $post)
    {
        try {
            // Delegate post update to the service
            $this->postService->updatePost($post, $request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Post updated!');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }

    /**
     * Remove the specified post from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post)
    {
        try {
            // Delegate post deletion to the service
            $this->postService->deletePost($post);

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Post deleted.');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }

    /**
     * Upload a cover image for the specified post.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCover(Request $request, Post $post)
    {
        try {
            // Delegate file upload to the service
            $url = $this->postService->uploadCover($post, $request->file('file'));

            // Return the file URL in the response
            return response()->json(['location' => $url]);
        } catch (Exception $e) {
            // Handle exceptions and return an error response
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the cover image for the specified post.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeCover(Request $request, Post $post)
    {
        try {
            // Delegate cover removal to the service
            $this->postService->removeCover($post);

            // Redirect back
            return redirect()->back();
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }
}
