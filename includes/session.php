<?php
// Controle de session : inclus en PREMIERE ligne de chaque page interne.
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

demarrer_session();

if (!est_connecte()) {
    rediriger('login.php');
}

// Interdit au navigateur de garder la page : le bouton "retour" ne rouvre rien.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
