<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    public function subscribe(SubscribeNewsletterRequest $request): JsonResponse
    {
        $email = strtolower($request->validated('email'));

        NewsletterSubscriber::query()->firstOrCreate(
            ['email' => $email],
            ['source' => 'footer', 'subscribed_at' => now()],
        );

        return response()->json([
            'message' => 'Merci ! Vous êtes inscrit à la newsletter C7Pourt3.',
        ]);
    }
}
