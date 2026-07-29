<?php

namespace Tests\Feature;

use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Tests\TestCase;

class LocalizedValidationTest extends TestCase
{
    public function test_validation_messages_are_displayed_in_french(): void
    {
        $this->assertLocalizedEmailValidation('fr', 'L’adresse e-mail est obligatoire.');
    }

    public function test_validation_messages_are_displayed_in_english(): void
    {
        $this->assertLocalizedEmailValidation('en', 'The email address is required.');
    }

    public function test_validation_messages_are_displayed_in_bambara(): void
    {
        $this->assertLocalizedEmailValidation('bm', 'Email wajibiyalen don.');
    }

    public function test_an_unsupported_language_falls_back_to_the_default_language(): void
    {
        $this->assertLocalizedEmailValidation('xx', 'L’adresse e-mail est obligatoire.');
    }

    public function test_the_browser_locale_cookie_is_not_discarded_as_an_encrypted_cookie(): void
    {
        $request = Request::create('/', 'GET', [], [
            'barasira_locale' => 'en',
        ]);

        $response = app(EncryptCookies::class)->handle(
            $request,
            fn (Request $request) => response()->json([
                'locale' => $request->cookie('barasira_locale'),
            ])
        );

        $this->assertSame('en', $response->getData(true)['locale']);
    }

    private function assertLocalizedEmailValidation(string $locale, string $message): void
    {
        $request = Request::create('/', 'POST', [], [
            'barasira_locale' => $locale,
        ]);

        $response = (new SetLocale)->handle($request, function () {
            $validator = validator([], [
                'email' => ['required', 'email'],
            ]);

            return response()->json([
                'message' => $validator->errors()->first('email'),
            ]);
        });

        $this->assertSame($message, $response->getData(true)['message']);
    }
}
