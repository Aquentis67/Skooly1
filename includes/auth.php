<?php
// Session et authentification (F1).
require_once __DIR__ . '/fonctions_utilisateur.php';

// Empreinte bcrypt d'un mot de passe sans importance : sert a faire le meme
// travail de verification quand l'identifiant n'existe pas (reponse identique).
const HASH_FACTICE = '$2y$10$CP3/hr7ikqyQUsYwd8dCQuSQm1r2dBYqK3B16ETlZkvOPqHhCGHKu';

function demarrer_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    ini_set('session.use_strict_mode', '1');   // refuse un identifiant de session invente
    ini_set('session.use_only_cookies', '1');
    session_name('SKOOLYSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,                    // inaccessible depuis JavaScript
        'samesite' => 'Lax',
    ]);
    session_start();
}

function est_connecte(): bool
{
    return !empty($_SESSION['utilisateur_id']);
}

// Retourne l'utilisateur si identifiant + mot de passe sont bons, sinon null.
function verifier_identifiants(PDO $pdo, string $identifiant, string $mot_de_passe): ?array
{
    $utilisateur = trouver_utilisateur($pdo, $identifiant);
    $hash = $utilisateur['mot_de_passe_hash'] ?? HASH_FACTICE;
    $ok = password_verify($mot_de_passe, $hash);
    if ($utilisateur === null || !$ok) {
        return null;
    }
    if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
        maj_hash_utilisateur($pdo, (int) $utilisateur['id'], password_hash($mot_de_passe, PASSWORD_DEFAULT));
    }
    return $utilisateur;
}

function ouvrir_session(array $utilisateur): void
{
    session_regenerate_id(true);               // nouvel identifiant apres connexion
    $_SESSION['utilisateur_id'] = (int) $utilisateur['id'];
    $_SESSION['identifiant']    = $utilisateur['identifiant'];
}

function fermer_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {      // supprime le cookie cote navigateur
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000, 'path' => $p['path'], 'domain' => $p['domain'],
            'secure' => $p['secure'], 'httponly' => $p['httponly'], 'samesite' => $p['samesite'],
        ]);
    }
    session_destroy();                         // detruit la session cote serveur
}
