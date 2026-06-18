<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    /**
     * Prompt système de contexte strict pour l'assistant IA C7PourT3.
     */
    private const SYSTEM_PROMPT = <<<'PROMPT'
Tu es l'assistant de luxe C7PourT3, la marque exclusive de sacs et maroquinerie basée à Casablanca, Maroc.

🎯 Ton rôle :
- Conseiller les clients sur nos collections de sacs de luxe.
- Recommander des produits en fonction des goûts, occasions et budgets des clients.
- Confirmer que la livraison se fait sous 48h partout au Maroc.
- Fournir des informations sur les matériaux, coloris et disponibilité.

📋 Règles strictes :
- Réponds TOUJOURS en français.
- Sois chaleureux, professionnel et luxueux dans ton ton.
- Ne recommande QUE des produits de notre catalogue (fourni en contexte).
- Mentionne toujours la livraison gratuite sous 48h au Maroc.
- Si le client demande quelque chose hors de ton domaine, redirige poliment vers notre service client WhatsApp.
- Formate tes réponses de manière claire et élégante.
PROMPT;

    /**
     * GET/POST /api/ai/recommend
     *
     * Reçoit un message utilisateur, enrichit avec le catalogue produits,
     * puis appelle l'API Gemini (ou renvoie une réponse simulée en fallback).
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $validated['message'];

        // Récupérer le catalogue produits pour le contexte
        $catalog = $this->buildCatalogContext();

        // Construire le prompt complet avec le catalogue
        $fullSystemPrompt = self::SYSTEM_PROMPT . "\n\n📦 CATALOGUE ACTUEL C7POURT3 :\n" . $catalog;

        // Tenter l'appel API Gemini, sinon fallback en simulation locale
        $apiKey = config('services.gemini.api_key');

        if ($apiKey) {
            $response = $this->callGeminiApi($apiKey, $fullSystemPrompt, $userMessage);
        } else {
            $response = $this->simulateResponse($userMessage, $catalog);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'reply' => $response,
                'context' => [
                    'brand' => 'C7PourT3',
                    'delivery' => 'Livraison sous 48h au Maroc',
                    'city' => 'Casablanca',
                    'currency' => 'MAD',
                ],
            ],
        ]);
    }

    /**
     * Construit le contexte du catalogue à injecter dans le prompt IA.
     */
    private function buildCatalogContext(): string
    {
        $products = Product::with(['variants' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        if ($products->isEmpty()) {
            return "Aucun produit disponible actuellement.";
        }

        return $products->map(function (Product $product) {
            $price = number_format($product->base_price / 100, 2) . ' MAD';
            $colors = $product->variants
                ->pluck('color')
                ->filter()
                ->unique()
                ->implode(', ');

            $inStock = $product->stock_morocco > 0 ? 'En stock' : 'Rupture de stock';
            $category = $product->category ?? 'Non catégorisé';

            $line = "• {$product->name} — {$price} | Catégorie: {$category} | {$inStock}";
            if ($colors) {
                $line .= " | Coloris: {$colors}";
            }
            if ($product->description) {
                $line .= "\n  Description: " . \Illuminate\Support\Str::limit($product->description, 120);
            }

            return $line;
        })->implode("\n");
    }

    /**
     * Appelle l'API Google Gemini pour générer la réponse.
     */
    private function callGeminiApi(string $apiKey, string $systemPrompt, string $userMessage): string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemPrompt],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $userMessage],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                    'topP' => 0.9,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text']
                    ?? 'Je suis désolé, je n\'ai pas pu générer une réponse. Veuillez réessayer.';
            }

            Log::warning('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->simulateResponse($userMessage, '');

        } catch (\Exception $e) {
            Log::error('Gemini API exception', ['error' => $e->getMessage()]);

            return $this->simulateResponse($userMessage, '');
        }
    }

    /**
     * Réponse simulée locale (fallback si pas de clé API).
     * Fournit des recommandations basées sur des mots-clés simples.
     */
    private function simulateResponse(string $userMessage, string $catalog): string
    {
        $message = mb_strtolower($userMessage);

        $products = Product::with(['variants' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        // Détection d'intention simple
        if (str_contains($message, 'livraison') || str_contains($message, 'délai') || str_contains($message, 'expédition')) {
            return "✨ **Livraison C7PourT3** ✨\n\n"
                . "Nous livrons partout au Maroc sous **48 heures** ! 🚚\n\n"
                . "📍 Depuis notre entrepôt de Casablanca, votre commande est préparée avec soin et livrée directement à votre porte.\n\n"
                . "La livraison est offerte pour toute commande. Vous pouvez payer en espèces à la réception (COD).";
        }

        if (str_contains($message, 'prix') || str_contains($message, 'combien') || str_contains($message, 'budget')) {
            $productList = $products->map(function ($p) {
                $price = number_format($p->base_price / 100, 2);
                return "• **{$p->name}** — {$price} MAD";
            })->implode("\n");

            return "💎 **Nos tarifs C7PourT3** 💎\n\n"
                . $productList . "\n\n"
                . "🚚 Livraison gratuite sous 48h partout au Maroc.\n"
                . "💳 Paiement à la livraison (COD) accepté.";
        }

        if (str_contains($message, 'couleur') || str_contains($message, 'coloris') || str_contains($message, 'teinte')) {
            $colorInfo = $products->map(function ($p) {
                $colors = $p->variants->pluck('color')->filter()->unique()->implode(', ');
                return $colors ? "• **{$p->name}** : {$colors}" : null;
            })->filter()->implode("\n");

            return "🎨 **Coloris disponibles** 🎨\n\n"
                . ($colorInfo ?: "Consultez notre collection pour voir tous les coloris.") . "\n\n"
                . "Chaque sac est disponible dans des teintes soigneusement sélectionnées pour refléter l'élégance marocaine.";
        }

        // Recommandation générique
        $featured = $products->where('is_featured', true)->take(3);
        if ($featured->isEmpty()) {
            $featured = $products->take(3);
        }

        $recommendations = $featured->map(function ($p) {
            $price = number_format($p->base_price / 100, 2);
            $colors = $p->variants->pluck('color')->filter()->unique()->take(3)->implode(', ');
            $line = "• **{$p->name}** — {$price} MAD";
            if ($colors) {
                $line .= " (coloris : {$colors})";
            }
            return $line;
        })->implode("\n");

        return "✨ **Bienvenue chez C7PourT3** ✨\n\n"
            . "Je suis ravie de vous aider ! Voici nos coups de cœur du moment :\n\n"
            . $recommendations . "\n\n"
            . "🚚 **Livraison offerte sous 48h** partout au Maroc.\n"
            . "💳 Paiement à la livraison (COD) disponible.\n\n"
            . "N'hésitez pas à me demander des détails sur un modèle en particulier ! 💼";
    }
}
