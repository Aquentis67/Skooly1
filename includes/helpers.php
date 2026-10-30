<?php
// Petites fonctions utilitaires partagees par toutes les pages.

const TAILLE_PAGE = 15;   // nombre de lignes par page dans les listes

// Echappe une valeur avant de l'ecrire dans une page HTML (protege du XSS).
function e($valeur): string
{
    return htmlspecialchars((string) ($valeur ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Nombre de caracteres (pas d'octets) ; fonctionne meme sans l'extension mbstring.
function longueur(string $texte): int
{
    return function_exists('mb_strlen') ? mb_strlen($texte, 'UTF-8') : (int) preg_match_all('/./su', $texte);
}

function rediriger(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function requete_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// ---------------------------------------------------------------- messages
function flash_ajouter(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_lire(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

// -------------------------------------------------------------------- CSRF
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_champ(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verifier(): void
{
    $recu = $_POST['csrf'] ?? '';
    if (!is_string($recu) || !hash_equals($_SESSION['csrf'] ?? '', $recu)) {
        http_response_code(400);
        exit('Requête refusée (jeton de sécurité invalide). Revenez en arrière et réessayez.');
    }
}

// ---------------------------------------------------------- lecture entrees
// Lit des champs POST sous forme de chaines nettoyees (jamais de tableau).
function lire_post(array $champs): array
{
    $valeurs = [];
    foreach ($champs as $champ) {
        $v = $_POST[$champ] ?? '';
        $valeurs[$champ] = is_string($v) ? trim($v) : '';
    }
    return $valeurs;
}

function param_get(string $cle, string $defaut = ''): string
{
    $v = $_GET[$cle] ?? $defaut;
    return is_string($v) ? trim($v) : $defaut;
}

// Entier strictement positif ou null (ex. ?id=12).
function param_id(string $cle = 'id'): ?int
{
    $n = filter_var(param_get($cle), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $n === false ? null : $n;
}

// ------------------------------------------------------------------ formats
function date_fr(?string $sql): string
{
    if (!$sql) {
        return '';
    }
    $d = DateTime::createFromFormat('Y-m-d', $sql);
    return $d ? $d->format('d/m/Y') : '';
}

// "31/12/2026" -> "2026-12-31" ; null si le format ou la date est impossible.
function date_vers_sql(string $fr): ?string
{
    if (!preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $fr, $m)) {
        return null;
    }
    if (!checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
        return null;
    }
    return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
}

function euros($v): string
{
    return $v === null || $v === '' ? '' : number_format((float) $v, 2, ',', ' ') . ' €/h';
}

function heures($v): string
{
    $s = number_format((float) $v, 2, ',', '');
    return rtrim(rtrim($s, '0'), ',') . ' h';
}

// "06.12 34 56 78" ou "+33 6 12 34 56 78" -> "06 12 34 56 78" (null si invalide)
function normaliser_telephone(string $tel): ?string
{
    $chiffres = preg_replace('/\D/', '', $tel);
    if (str_starts_with(trim($tel), '+33')) {
        $chiffres = '0' . substr($chiffres, 2);
    }
    if (!preg_match('/^0[1-9]\d{8}$/', $chiffres)) {
        return null;
    }
    return trim(chunk_split($chiffres, 2, ' '));
}

// ----------------------------------------------------- tri et pagination
// $colonnes : liste blanche  cle publique => expression SQL.
// Un nom inconnu dans l'adresse retombe sur le tri par defaut, sans erreur.
function lire_tri(array $colonnes, string $defaut, string $prefixe = ''): array
{
    $tri = param_get($prefixe . 'tri');
    if (!array_key_exists($tri, $colonnes)) {
        $tri = $defaut;
    }
    $ordre = param_get($prefixe . 'ordre') === 'desc' ? 'DESC' : 'ASC';
    return [$tri, $ordre];
}

function lire_page(string $prefixe = ''): int
{
    $n = filter_var(param_get($prefixe . 'page', '1'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $n === false ? 1 : $n;
}

// Echappe % et _ pour un LIKE "contient".
function motif_like(string $terme): string
{
    return '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $terme) . '%';
}

function pagination_infos(int $total, int $page, int $taille = TAILLE_PAGE): array
{
    $nb_pages = max(1, (int) ceil($total / $taille));
    $page = min(max(1, $page), $nb_pages);
    return ['total' => $total, 'page' => $page, 'nb_pages' => $nb_pages,
            'taille' => $taille, 'decalage' => ($page - 1) * $taille];
}

// Execute la requete de comptage puis la requete de la page demandee.
// $sql_select contient deja son ORDER BY (construit depuis la liste blanche).
function requete_paginee(PDO $pdo, string $sql_comptage, string $sql_select, array $params, int $page, int $taille = TAILLE_PAGE): array
{
    $st = $pdo->prepare($sql_comptage);
    $st->execute($params);
    $pagination = pagination_infos((int) $st->fetchColumn(), $page, $taille);

    $st = $pdo->prepare($sql_select . ' LIMIT :limite OFFSET :decalage');
    foreach ($params as $nom => $valeur) {
        $st->bindValue($nom, $valeur, is_int($valeur) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $st->bindValue(':limite', $pagination['taille'], PDO::PARAM_INT);
    $st->bindValue(':decalage', $pagination['decalage'], PDO::PARAM_INT);
    $st->execute();
    return [$st->fetchAll(), $pagination];
}

// URL de la page courante avec quelques parametres modifies (null = retire).
function url_avec(array $modifs): string
{
    $q = array_merge($_GET, $modifs);
    foreach ($q as $k => $v) {
        if ($v === null || $v === '') {
            unset($q[$k]);
        }
    }
    return basename($_SERVER['PHP_SELF']) . ($q ? '?' . http_build_query($q) : '');
}
