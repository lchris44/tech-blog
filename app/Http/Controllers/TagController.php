<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use App\Services\Datatable\DatatableService;
use App\Services\TagService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    protected $tagService;

    /**
     * Constructor to inject TagService.
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Display a paginated list of tags.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Query tags
        $query = Tag::query();

        // Apply default ordering if no datatable parameters are provided
        if (! $request->has('dt_params')) {
            $query->orderBy('id', 'desc');
        }

        // Use Datatables service to format the response
        $tags = DatatableService::of($query)->make();

        // Render the tags index page with the payload
        return Inertia::render('Dashboard/Tags/Index', [
            'payload' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new tag.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        // Initialize a new tag with default values
        $tag = new Tag;

        // Render the edit modal with the tag
        return Inertia::modal('Dashboard/Tags/Edit', [
            'tag' => $tag,
        ])
            ->baseRoute('tags.index')
            ->refreshBackdrop();
    }

    /**
     * Store a newly created tag in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TagRequest $request)
    {
        try {
            // Delegate tag creation to the service
            $this->tagService->createTag($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Tag Created!');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified tag.
     *
     * @return \Inertia\Response
     */
    public function edit(Tag $tag)
    {
        // Render the edit modal with the tag
        return Inertia::modal('Dashboard/Tags/Edit', [
            'tag' => $tag,
        ])
            ->baseRoute('tags.index')
            ->refreshBackdrop();
    }

    /**
     * Update the specified tag in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TagRequest $request, Tag $tag)
    {
        try {
            // Delegate tag update to the service
            $this->tagService->updateTag($tag, $request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Tag updated!');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }

    /**
     * Remove the specified tag from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tag $tag)
    {
        try {
            // Delegate tag deletion to the service
            $this->tagService->deleteTag($tag);

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Tag deleted.');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }
}
