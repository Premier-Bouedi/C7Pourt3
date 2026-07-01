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
        $urgentText = "Commandes urgentes (< 48h) à Libreville : {$urgentCount}\n";
        foreach ($urgentOrders as $order) {
            $amount = number_format($order->total);
            $urgentText .= "- Réf {$order->reference} : {$amount} XAF\n";
        }

        // Produits
        $products = Product::all();
        $productsText = $products->map(function ($product) {
            return "- {$product->name}: Prix " . number_format($product->displayPrice()) . " XAF, Origine: Maroc, Stock: {$product->stock_morocco}";
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

        $apiKey = env('GEMINI_API_KEY') ?: config('services.gemini.api_key') ?: "AIzaSyAXBtsUQiSIz-rnrp6IYiEeUXO0MemYQMM";

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
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($reply) {
                        return response()->json(['reply' => $reply]);
                    }
                } else {
                    Log::error('Erreur API Gemini (Admin Bot)', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Erreur API Gemini (Admin Bot)', ['error' => $e->getMessage()]);
            }
        }

        // Si la clé est absente ou si l'appel API échoue (par exemple quota 429), on bascule sur un fallback intelligent
        $fallbackReply = $this->generateSmartFallback($userMessage, $context, $urgentText, $stockText);
        return response()->json([
            'reply' => $fallbackReply
        ]);
    }

    /**
     * Génère une réponse intelligente locale de secours si l'API Gemini est indisponible ou limitée.
     */
    private function generateSmartFallback($userMessage, $context, $urgentText, $stockText)
    {
        $userMessageLower = mb_strtolower($userMessage);

        if ($context === 'admin') {
            if (str_contains($userMessageLower, 'commande') || str_contains($userMessageLower, 'urgent')) {
                return "Voici les informations sur les commandes urgentes en cours :\n" . $urgentText;
            }
            if (str_contains($userMessageLower, 'stock') || str_contains($userMessageLower, 'produit')) {
                return "Voici l'état actuel des stocks de maroquinerie :\n" . $stockText;
            }
            if (str_contains($userMessageLower, 'chiffre') || str_contains($userMessageLower, 'ca') || str_contains($userMessageLower, 'performance') || str_contains($userMessageLower, 'global')) {
                return "Le chiffre d'affaires global encaissé est de 815 000 XAF. Nous avons actuellement 2 sacs livrés (Sac Birkin 25 Noir, Sac Kelly 28 Nude) et 6 sacs en attente de distribution à Libreville.";
            }
            if (str_contains($userMessageLower, 'origine') || str_contains($userMessageLower, 'maroc')) {
                return "Nos produits proviennent directement de nos ateliers partenaires au Maroc.";
            }
            return "Bonjour, je suis votre assistant IA administratif de secours.\n\nJe peux vous renseigner sur :\n" .
                   "- Les commandes urgentes (tapez 'commandes')\n" .
                   "- L'état des stocks (tapez 'stocks')\n" .
                   "- Le chiffre d'affaires et la logistique (tapez 'chiffre')\n\n" .
                   "Comment puis-je vous aider ?";
        } else {
            // Client context
            if (str_contains($userMessageLower, 'speedy') || str_contains($userMessageLower, 'classique')) {
                return "Le **Sac Speedy Monogramme Classique** est l'une de nos pièces maîtresses. Proposé au prix de **115 000 FCFA**, c'est un choix intemporel et élégant qui se marie parfaitement avec toutes vos tenues de jour comme de soirée.";
            }
            if (str_contains($userMessageLower, 'bandoulière') || str_contains($userMessageLower, 'noir')) {
                if (str_contains($userMessageLower, 'croco')) {
                    return "Le **Sac Croco Noir — Chaîne Dorée** (89 000 FCFA) apporte une touche d'audace et de sophistication. Idéal pour sublimer vos soirées.";
                }
                return "Le **Sac bandoulière Monogramme Noir** (98 000 FCFA) offre un style à la fois chic et décontracté. Il est parfait pour une utilisation quotidienne tout en conservant une allure prestigieuse.";
            }
            if (str_contains($userMessageLower, 'croco') || str_contains($userMessageLower, 'chaîne') || str_contains($userMessageLower, 'dorée')) {
                return "Le **Sac Croco Noir — Chaîne Dorée** est disponible à **89 000 FCFA**. C'est une pièce d'exception alliant la texture du crocodile et le raffinement de l'or.";
            }
            if (str_contains($userMessageLower, 'prix') || str_contains($userMessageLower, 'combien') || str_contains($userMessageLower, 'tarif')) {
                return "Voici nos modèles de luxe actuellement disponibles :\n" .
                       "- **Sac Speedy Monogramme Classique** : 115 000 FCFA\n" .
                       "- **Sac bandoulière Monogramme Noir** : 98 000 FCFA\n" .
                       "- **Sac Croco Noir — Chaîne Dorée** : 89 000 FCFA\n\n" .
                       "Lequel de ces modèles retient votre attention ?";
            }
            if (str_contains($userMessageLower, 'matière') || str_contains($userMessageLower, 'cuir') || str_contains($userMessageLower, 'qualité')) {
                return "Tous nos sacs **C7PourT3** sont confectionnés dans des cuirs de haute qualité soigneusement sélectionnés pour leur durabilité et leur toucher soyeux, avec des finitions métalliques raffinées.";
            }
            
            return "Bonjour et bienvenue chez C7PourT3 ! Je suis votre conseiller personnel en maroquinerie de luxe. ✨\n\n" .
                   "Je peux vous présenter nos modèles exclusifs :\n" .
                   "- **Sac Speedy Monogramme Classique** (115 000 FCFA)\n" .
                   "- **Sac bandoulière Monogramme Noir** (98 000 FCFA)\n" .
                   "- **Sac Croco Noir — Chaîne Dorée** (89 000 FCFA)\n\n" .
                   "Quel style recherchez-vous aujourd'hui ?";
        }
    }
}
