<?php

namespace App\Http\Middleware;

use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'seo' => SeoMeta::defaults($request),
            'auth' => [
                'user' => $user
                    ? [
                        'id' => $user->id,
                        'firstname' => Str::title($user->first_name),
                        'lastname' => Str::title($user->last_name),
                        'name' => Str::title($user->first_name).' '.Str::title($user->last_name),
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                    ]
                    : null,
                'impersonation' => $request->session()->get('impersonator'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
