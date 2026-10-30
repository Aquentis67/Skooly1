<?php
// Ce que toutes les pages internes utilisent.
require_once __DIR__ . '/constantes.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/formulaire.php';
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/vues.php';

date_default_timezone_set('Europe/Paris');

// Aucune erreur technique n'est montree a l'utilisateur (sauf debug = true).
set_exception_handler(function (Throwable $t): void {
    error_log((string) $t);
    http_response_code(500);
    $detail = '';
    try {
        if (config()['debug']) {
            $detail = '<pre>' . htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
    } catch (Throwable $ignore) {
    }
    echo '<!DOCTYPE html><meta charset="utf-8"><title>Erreur</title>'
       . '<h1>Une erreur est survenue</h1><p>Réessayez, ou contactez le service informatique.</p>' . $detail;
});
