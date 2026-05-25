<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'app' => [
                'name' => 'C7Pourt3',
                'whatsapp' => config('c7pourt3.whatsapp_number'),
                'facebook' => config('c7pourt3.facebook_url'),
                'developer' => [
                    'name' => config('c7pourt3.developer_name'),
                    'url' => config('c7pourt3.developer_url'),
                ],
            ],
        ]);
    }
}
