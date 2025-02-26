<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeLanguageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Change the application's locale and store it in the session.
     */
    public function changeLanguage(ChangeLanguageRequest $request): RedirectResponse
    {
        // Get the selected locale from the request
        $locale = $request->get('lang');

        // Store the locale in the session
        session(['locale' => $locale]);

        // Set the application's locale
        App::setLocale($locale);

        // Redirect back with a success message
        return redirect()->back()->with([
            'success' => 'Language changed successfully.',
        ]);
    }
}
