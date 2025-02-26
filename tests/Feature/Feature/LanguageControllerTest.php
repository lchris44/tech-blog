<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test (if needed)

    /**
     * Test changing the application language.
     *
     * @return void
     */
    public function test_change_language()
    {
        // Define the new locale
        $newLocale = 'it'; // Italian

        // Make a POST request to the changeLanguage endpoint
        $response = $this->post(route('language.change'), [
            'lang' => $newLocale,
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the locale was set in the session
        $this->assertEquals($newLocale, Session::get('locale'));

        // Assert that the application locale was updated
        $this->assertEquals($newLocale, App::getLocale());

        // Assert that the response contains a success message
        $response->assertSessionHas('success', 'Language changed successfully.');
    }

    /**
     * Test changing the application language with invalid data.
     *
     * @return void
     */
    public function test_change_language_with_invalid_data()
    {
        // Make a POST request to the changeLanguage endpoint with invalid data
        $response = $this->post(route('language.change'), [
            'lang' => 'invalid_locale', // Invalid locale
        ]);

        // Assert that the response status is 302 (redirect with validation errors)
        $response->assertStatus(302);

        // Assert that the session contains validation errors
        $response->assertSessionHasErrors(['lang']);
    }

    /**
     * Test changing the application language without providing a locale.
     *
     * @return void
     */
    public function test_change_language_without_locale()
    {
        // Make a POST request to the changeLanguage endpoint without a locale
        $response = $this->post(route('language.change'), [
            // No 'lang' key provided
        ]);

        // Assert that the response status is 302 (redirect with validation errors)
        $response->assertStatus(302);

        // Assert that the session contains validation errors
        $response->assertSessionHasErrors(['lang']);
    }
}
