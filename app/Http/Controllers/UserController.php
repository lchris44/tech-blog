<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\Datatable\DatatableService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $userService;

    /**
     * Constructor to inject UserService.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a paginated list of users.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Query users
        $query = User::query();

        // Apply default ordering if no datatable parameters are provided
        if (! $request->has('dt_params')) {
            $query->orderBy('id', 'desc');
        }

        // Use DatatableService to format the response
        $users = DatatableService::of($query)->make();

        // Render the users index page with the payload
        return Inertia::render('Dashboard/Users/Index', [
            'payload' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        // Initialize a new user
        $user = new User;

        // Render the edit modal with the user
        return Inertia::modal('Dashboard/Users/Edit', [
            'user' => $user,
        ])
            ->baseRoute('users.index')
            ->refreshBackdrop();
    }

    /**
     * Store a newly created user in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        try {
            // Delegate user creation to the service
            $this->userService->createUser($request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('success', 'User Created!');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        // Render the edit modal with the user
        return Inertia::modal('Dashboard/Users/Edit', [
            'user' => $user,
        ])
            ->baseRoute('users.index')
            ->refreshBackdrop();
    }

    /**
     * Update the specified user in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            // Delegate user update to the service
            $this->userService->updateUser($user, $request->validated());

            // Redirect back with a success message
            return redirect()->back()->with('success', 'User updated!');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        try {
            // Delegate user deletion to the service
            $this->userService->deleteUser($user);

            // Redirect back with a success message
            return redirect()->back()->with('success', 'User deleted.');
        } catch (Exception $e) {
            // Handle exceptions and redirect back with an error message
            return back()->with('error', 'There was an error processing your request. Please try again.');
        }
    }
}
