<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>C7Pourt3 — Sacs de luxe</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-stone-50 text-stone-900">
    @inertia

    @php
        $isAdmin = request()->is('admin*');
        $positionClass = $isAdmin ? 'right-6 items-end' : 'left-6 items-start';
        $greeting = $isAdmin 
            ? 'Bonjour ! Je suis prêt à analyser les commandes et le stock.' 
            : 'Bonjour et bienvenue chez C7PourT3 ! Je suis votre conseiller personnel en maroquinerie de luxe. Que recherchez-vous aujourd\'hui ?';
    @endphp

    <!-- Widget Assistant IA PFE (Bulle circulaire) -->
    <div id="ai-bot-wrapper" class="fixed bottom-6 {{ $positionClass }} z-[9999] flex flex-col">
        <!-- Conteneur de Chat (Masqué par défaut) -->
        <div id="ai-chat-window" class="hidden mb-4 w-80 bg-stone-900 text-white rounded-2xl shadow-2xl border border-orange-500/30 overflow-hidden flex flex-col" style="height: 400px;">
            <div class="bg-stone-950 px-4 py-3 border-b border-orange-500/30 flex justify-between items-center">
                <span class="font-semibold text-sm text-orange-400">Assistant IA PFE</span>
                <button id="close-ai-chat" class="text-stone-400 hover:text-white">&times;</button>
            </div>
            <div id="ai-chat-container" class="flex-1 p-4 overflow-y-auto text-sm space-y-3 bg-stone-900">
                <div class="text-left">
                    <span class="bg-stone-800 text-stone-200 px-3 py-2 rounded-lg inline-block">{{ $greeting }}</span>
                </div>
            </div>
            <div class="p-3 bg-stone-950 border-t border-stone-800">
                <div class="flex gap-2">
                    <input type="text" id="ai-chat-input" placeholder="Poser une question..." class="flex-1 bg-stone-800 border-none rounded-full px-4 py-2 text-sm text-white focus:ring-1 focus:ring-orange-500 outline-none" autocomplete="off">
                    <button id="ai-send-btn" class="bg-orange-500 hover:bg-orange-600 text-stone-900 rounded-full px-4 py-2 font-bold text-sm transition-colors ai-send-btn">
                        &rarr;
                    </button>
                </div>
            </div>
        </div>

        <!-- Bouton Bulle Principal -->
        <button id="toggle-ai-chat" class="w-20 h-20 rounded-full flex flex-col items-center justify-center bg-black text-white shadow-xl z-50 transition-all hover:scale-105">
            <span class="text-xs uppercase tracking-wider font-semibold">ASSISTANT IA</span>
            <span class="text-[10px] mt-1 text-emerald-400 font-medium">Prête</span>
        </button>
    </div>

    <!-- Logique JS du Bot IA (Top Navbar / Bulle Ronde) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Formatteur Markdown simple côté client
            function formatMarkdown(text) {
                if (!text) return "";
                let html = text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;");
                
                // Formater le gras (**texte**)
                html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                // Formater l'italique (*texte*)
                html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
                // Formater les listes à puces
                html = html.replace(/^\s*[-*•]\s+(.+)/gm, '• $1');
                // Retours à la ligne
                html = html.replace(/\n/g, '<br>');
                return html;
            }

            // Toggle de la fenêtre de chat
            const toggleBtn = document.getElementById('toggle-ai-chat');
            const closeBtn = document.getElementById('close-ai-chat');
            const chatWindow = document.getElementById('ai-chat-window');

            if (toggleBtn && chatWindow) {
                toggleBtn.addEventListener('click', () => {
                    chatWindow.classList.toggle('hidden');
                });
            }
            if (closeBtn && chatWindow) {
                closeBtn.addEventListener('click', () => {
                    chatWindow.classList.add('hidden');
                });
            }

            // Intercepter la touche "Entrée" dans le champ de texte
            document.body.addEventListener('keydown', function (e) {
                const inputField = e.target.closest('.ai-chat-input, #ai-chat-input');
                if (inputField && e.key === 'Enter') {
                    e.preventDefault();
                    const botWrapper = inputField.closest('.ai-bot-wrapper, #ai-bot-wrapper') || document;
                    const sendBtn = botWrapper.querySelector('.ai-send-btn, #ai-send-btn, [data-ai-action="send"], .btn-envoyer-ia');
                    if (sendBtn) {
                        sendBtn.click();
                    }
                }
            });

            // Utilisation de la délégation d'événements pour intercepter les interactions du Bot IA
            // Cela permet de supporter les éléments générés dynamiquement (React/Inertia/Filament)
            document.body.addEventListener('click', async function (e) {
                // Intercepter le bouton "Envoyer" ou "Lancer Chatbot IA"
                const sendBtn = e.target.closest('.ai-send-btn, #ai-send-btn, [data-ai-action="send"], .btn-envoyer-ia');
                
                if (sendBtn) {
                    e.preventDefault();
                    
                    // Cibler dynamiquement le conteneur de chat et l'input
                    const botWrapper = sendBtn.closest('.ai-bot-wrapper, #ai-bot-wrapper') || document;
                    const inputField = botWrapper.querySelector('.ai-chat-input, #ai-chat-input, input[name="ai_message"]');
                    const chatContainer = botWrapper.querySelector('.ai-chat-container, #ai-chat-container, .messages-list');
                    
                    if (!inputField || !inputField.value.trim()) return;
                    
                    const message = inputField.value.trim();
                    inputField.value = ''; // Reset de l'input
                    
                    // Injection visuelle (message utilisateur) sans casser le design
                    if (chatContainer) {
                        const userMsgHtml = `<div class="text-right my-2"><span class="bg-blue-600 text-white px-3 py-2 rounded-lg inline-block text-sm">${message}</span></div>`;
                        chatContainer.insertAdjacentHTML('beforeend', userMsgHtml);
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                        
                        // Loader IA
                        const loaderHtml = `<div class="ai-loader text-left my-2"><span class="bg-stone-200 text-stone-600 px-3 py-2 rounded-lg inline-block text-sm animate-pulse">...</span></div>`;
                        chatContainer.insertAdjacentHTML('beforeend', loaderHtml);
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                    
                    // Requête AJAX Fetch vers l'endpoint
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const response = await fetch('/admin/ai-assistant/ask', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ message: message, context: window.location.pathname.startsWith('/admin') ? 'admin' : 'client' })
                        });
                        
                        const data = await response.json();
                        
                        if (chatContainer) {
                            const loader = chatContainer.querySelector('.ai-loader');
                            if (loader) loader.remove();
                            
                            const aiReplyHtml = `<div class="text-left my-2"><span class="bg-stone-200 text-stone-800 px-3 py-2 rounded-lg inline-block text-sm">${formatMarkdown(data.reply)}</span></div>`;
                            chatContainer.insertAdjacentHTML('beforeend', aiReplyHtml);
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        }
                    } catch (error) {
                        console.error('Erreur API IA:', error);
                        if (chatContainer) {
                            const loader = chatContainer.querySelector('.ai-loader');
                            if (loader) loader.remove();
                            chatContainer.insertAdjacentHTML('beforeend', `<div class="text-left my-2"><span class="bg-red-100 text-red-600 px-3 py-2 rounded-lg inline-block text-sm">Erreur de connexion avec l'IA.</span></div>`);
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
