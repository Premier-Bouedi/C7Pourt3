<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

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

    /**
     * Display newsletter management page for admin
     */
    public function index(): Response
    {
        $subscribers = NewsletterSubscriber::orderByDesc('subscribed_at')->paginate(100);

        $stats = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::where('is_active', true)->count(),
            'this_month' => NewsletterSubscriber::where('subscribed_at', '>=', now()->startOfMonth())->count(),
        ];

        return Inertia::render('Newsletter/Index', [
            'subscribers' => $subscribers,
            'stats' => $stats,
        ]);
    }
}
