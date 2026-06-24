<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    /**
     * Traite la question de l'administrateur avec le contexte métier réel.
     */
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');

        // 1. Collecter le contexte (Données réelles)
        
        // Commandes urgentes (en attente, < 48h)
        $urgentOrders = Order::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(48))
            ->get();
            
        $urgentCount = $urgentOrders->count();
        $urgentText = "Commandes urgentes (< 48h) à Casablanca : {$urgentCount}\n";
        foreach ($urgentOrders as $order) {
            $amount = number_format($order->total_amount / 100, 2);
            $urgentText .= "- Réf {$order->reference} : {$amount} MAD\n";
        }

        // Produits
        $products = Product::all();
        $productsText = $products->map(function($product) {
            return "- {$product->name}: Prix {$product->price} XAF, Origine: {$product->origin}, Stock: {$product->stock_quantity}";
        })->implode("\n");
        $stockText = "État des stocks (Sacs haut de gamme) :\n" . $productsText . "\n";

        // Determine request origin (client front‑end or admin dashboard)
        $context = $request->input('context', 'client'); // default client
        if ($context === 'admin') {
            // Admin‑side context: use real‑time data already collected above
            $systemPrompt = "Tu es l'assistant IA interne pour les administrateurs de C7PourT3.\n"
                . "Ton rôle est d'analyser les stocks, la logistique et les performances financières.\n\n"
                . "=== CONTEXTE ACTUEL EN TEMPS RÉEL ===\n"
                . $urgentText . "\n"
                . $stockText . "\n"
                . "Livrés : 2 sacs (Sac Birkin 25 Noir, Sac Kelly 28 Nude)\n"
                . "Stock agence actuel : 6 sacs en attente de distribution à Libreville\n"
                . "Chiffre encaissé global : 815 000 XAF\n"
                . "Origine des produits : Maroc\n"
                . "=====================================\n\n"
                . "Réponds de manière professionnelle, précise et concise aux questions de l'administrateur.";
        } else {
            // Front‑end client context: present product catalogue as a luxury shop advisor with new welcome message
            $welcomeMessage = "Bonjour et bienvenue chez C7PourT3 ! Je suis votre conseiller personnel en maroquinerie de luxe. Comment puis-je vous aider à trouver le sac idéal aujourd'hui ?";
            $productInfo = "- Sac Speedy Monogramme Classique : 115 000 FCFA\n- Sac bandoulière Monogramme Noir : 98 000 FCFA\n- Sac Croco Noir — Chaîne Dorée : 89 000 FCFA";
            $systemPrompt = "Tu es le conseiller de vente haut de gamme pour les visiteurs du site C7PourT3.\n" .
                $welcomeMessage . "\n\n" .
                "Ton rôle est d'expliquer les matières, tailles, associations de tenues, et d'aider le client à finaliser son choix. Interdiction de parler de la gestion des stocks internes ou de l'interface admin.\n\n" .
                "=== CATALOGUE DE PRODUITS ===\n" . $productInfo . "\n=== FIN CATALOGUE ===\n" . "Réponds de façon élégante, concise et personnalisée, en mettant en avant le prestige des sacs.";
        }

        // 2. Appel à l'API IA (Simulation ou vraie API)
        $apiKey = config('services.gemini.api_key');

        if ($apiKey) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $userMessage]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Désolé, impossible de traiter la réponse.";
                    return response()->json(['reply' => $reply]);
                }
            } catch (\Exception $e) {
                Log::error('Erreur API Gemini (Admin Bot)', ['error' => $e->getMessage()]);
            }
        }

        // Fallback en cas d'absence de clé API ou d'erreur
        $reply = $this->simulateResponse($userMessage, $urgentCount);

        return response()->json([
            'reply' => $reply
        ]);
    }

    private function simulateResponse($message, $urgentCount)
    {
        $message = strtolower($message);
        
        if (str_contains($message, 'commande') || str_contains($message, 'urgent')) {
            return "Il y a actuellement {$urgentCount} commande(s) urgente(s) en attente pour Casablanca (délai < 48h).";
        }

        if (str_contains($message, 'stock')) {
            return "Les stocks sont à jour. Consultez l'onglet Stocks pour voir les détails de vos sacs haut de gamme.";
        }

        return "Ceci est une réponse simulée (API non configurée). J'ai bien reçu votre message : '{$message}'. "
            . "N'hésitez pas à me demander l'état des commandes urgentes ou des stocks.";
    }
}
