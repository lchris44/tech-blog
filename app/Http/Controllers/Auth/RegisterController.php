<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function index(): \Inertia\Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // Create a new user instance
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Log in the user
        Auth::login($user);

        // Regenerate session
        $request->session()->regenerate();

        return redirect(AppServiceProvider::HOME);
    }
}
