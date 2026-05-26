<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Root template yang akan dipakai Inertia.
     */
    protected $rootView = 'app';

    /**
     * Data yang dibagikan ke semua halaman (shared props).
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'            => $request->user()->id,
                    'name'          => $request->user()->name,
                    'email'         => $request->user()->email,
                    'role'          => $request->user()->role,
                    'nim'           => $request->user()->nim,
                    'program_studi' => $request->user()->program_studi,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
        ]);
    }
}
