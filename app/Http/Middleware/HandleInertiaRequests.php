<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = null;
        if (auth()->check()) {
            $user = (new UserResource(auth()->user()))->toArray($request);
            //$user = auth()->user()->toArray();
        }

        return [
            ...parent::share($request),
            /*'app' => [
                'name' => config('app.name'),
                'email' => settings('app.email'),
                'phone' => settings('app.phone'),
                'address' => settings('app.address'),
                'logo' => url(settings('app.logo', 'images/logo.png')),
                'socialLinks' => [
                    'facebook' => settings('seo.facebook_link'),
                    'instagram' => settings('seo.instagram_link'),
                    'twitter' => settings('seo.twitter_link'),
                    'linkedin' => settings('seo.linkedin_link'),
                ]
            ],*/
            'auth' => [
                'check' => auth()->check(),
                'user' => $user,
            ],
            /*'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],*/
        ];
    }
}
