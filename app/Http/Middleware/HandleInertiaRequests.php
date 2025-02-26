<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
        return [
            ...parent::share($request),
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'popstate' => false,
            'flushMessage' => function () use ($request) {
                foreach (['success', 'error', 'info', 'warning'] as $type) {

                    if ($message = $request->session()->get($type)) {
                        return [
                            'message' => $message,
                            'type' => $type,
                        ];
                    }
                }

                return [
                    'message' => null,
                    'type' => null,
                ];

            },
        ];
    }
}
