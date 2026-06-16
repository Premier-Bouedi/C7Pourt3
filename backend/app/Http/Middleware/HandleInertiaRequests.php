<?php

namespace App\Http\Middleware;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $wa = app(WhatsAppService::class);

        return array_merge(parent::share($request), [
            'app' => [
                'name' => 'C7Pourt3',
                'whatsapp' => $wa->number(),
                'facebook' => config('c7pourt3.facebook_url'),
                'instagram' => config('c7pourt3.instagram_url'),
                'whatsappUrls' => [
                    'general' => $wa->general(),
                    'checkout' => $wa->checkoutHelp(),
                ],
                'developer' => [
                    'name' => config('c7pourt3.developer_name'),
                    'url' => config('c7pourt3.developer_url'),
                ],
            ],
        ]);
    }
}
