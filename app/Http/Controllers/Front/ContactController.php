<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:150'],
            'user_type' => [
                'nullable',
                'string',
                Rule::in(['client', 'prestataire', 'visitor', 'partner']),
            ],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'consent' => ['accepted'],
        ]);

        try {
            Mail::to(config('mail.contact_address', 'contact@barasira.com'))
                ->send(new ContactMessageMail($validated));

            return back()->with(
                'success',
                'Votre message a bien été envoyé. Notre équipe vous répondra rapidement.'
            );
        } catch (Throwable $exception) {
            Log::error('Erreur lors de l’envoi du formulaire de contact.', [
                'exception' => $exception->getMessage(),
                'email' => $validated['email'],
            ]);

            return back()->with(
                'error',
                'Votre message n’a pas pu être envoyé. Veuillez réessayer ultérieurement.'
            );
        }
    }
}
