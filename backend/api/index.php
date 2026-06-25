<?php
// Point d'entrée Vercel - Laravel est dans ce même dossier (backend/)
define('LARAVEL_START', microtime(true));

// Vérification que les fichiers existent
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    echo 'Erreur: vendor/autoload.php introuvable. Vérifiez que composer install a été exécuté.';
    exit;
}

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
