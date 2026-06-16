# C7Pourt3 — Scripts de communication

## A. Flux WhatsApp (canal principal)

| # | Déclencheur | Implémentation |
|---|-------------|----------------|
| 1 | Clic « Commander via WhatsApp » (Quick View) | `WhatsAppService::orderInitiation()` → lien `wa.me` |
| 2 | Admin « Planifier livraison » | `OrderCommunicationService::sendDeliveryTodayPack()` → Message 2 |
| 3 | Commande livrée (admin ou auto) | `sendReviewRequestPack()` → Message 3 + lien `/avis?ref=` |

### Modèles texte (référence)

**Message 1** — Initiation commande  
`Bonjour C7Pourt3, je souhaite commander le sac [Modèle] au prix de [Prix] FCFA. Voici mes informations pour la livraison à Libreville : [Nom/Prénom] - [Quartier] - [Téléphone].`

**Message 2** — Confirmation & planification  
`Bonjour [Prénom], c'est l'équipe C7Pourt3. ✨ Nous avons bien préparé votre commande pour le modèle [Modèle]. Notre livreur passera demain entre [Heure] et [Heure]. Le montant total de [Prix] FCFA sera à régler en espèces à la livraison...`

**Message 3** — Demande d'avis  
`Bonjour [Prénom], nous espérons que votre nouveau sac C7Pourt3 vous plaît... [Lien_Avis_React]`

## B. Flux Email (canal luxe)

| Email | Objet | Template |
|-------|-------|----------|
| Confirmation | `Votre commande C7Pourt3 est validée ✨` | `emails/order-confirmed.blade.php` |
| Planification | Livraison planifiée | `emails/delivery-today.blade.php` |
| Avis | Partagez votre avis | `emails/review-invitation.blade.php` |

Design : fond sombre `#1c1917`, accents dorés `#d4af37`, police Georgia.

## C. Système d'avis

- Table `reviews` : note 1-5, commentaire, `is_approved`, `is_verified_purchase`
- Publication site : uniquement si `is_approved = true` (admin → **Gestion des avis**)
- Badge **Achat vérifié** : commande `delivered` + référence + téléphone sur `/avis`
- Front : onglet « Avis clients » dans l'aperçu rapide produit

## Configuration

```env
C7_WHATSAPP_NUMBER=241XXXXXXXX
MAIL_MAILER=log   # ou smtp / resend en production
```
