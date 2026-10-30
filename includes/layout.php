<?php
// Structure commune a toutes les pages (F8) : bandeau, menu, utilisateur, deconnexion.

function entete(string $titre, string $actif = '', bool $menu = true): void
{
    $liens = [
        'accueil'       => ['index.php', 'Tableau de bord'],
        'apprenants'    => ['apprenants.php', 'Apprenants'],
        'promotions'    => ['promotions.php', 'Promotions'],
        'intervenants'  => ['intervenants.php', 'Intervenants'],
        'matieres'      => ['matieres.php', 'Matières'],
        'salles'        => ['salles.php', 'Salles'],
        'interventions' => ['interventions.php', 'Interventions'],
    ];
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titre) ?> — SkoolY</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/skooly.css">
    <script src="assets/js/wired.bundle.js" defer></script>
</head>
<body>
<?php if ($menu) : ?>
<nav class="navbar navbar-expand-lg navbar-dark bandeau" aria-label="Navigation principale">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">Skool<span class="marque-y">Y</span>
            <small class="fw-normal opacity-75">Campus ExpertY</small></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-principal"
                aria-controls="menu-principal" aria-expanded="false" aria-label="Afficher le menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu-principal">
            <ul class="navbar-nav me-auto">
                <?php foreach ($liens as $cle => [$url, $texte]) : ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $cle === $actif ? ' active' : '' ?>" href="<?= e($url) ?>"
                           <?= $cle === $actif ? 'aria-current="page"' : '' ?>><?= e($texte) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <span class="navbar-text me-3">Connecté : <strong><?= e($_SESSION['identifiant'] ?? '') ?></strong></span>
            <a class="btn btn-outline-light btn-sm" href="logout.php">Déconnexion</a>
        </div>
    </div>
</nav>
<?php endif; ?>
<main class="container-xl my-4">
<?php
    foreach (flash_lire() as $m) {
        echo '<div class="alert alert-' . e($m['type']) . ' alert-dismissible fade show" role="alert">'
           . e($m['message'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button></div>';
    }
}

function pied(): void
{
    ?>
</main>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/skooly.js"></script>
</body>
</html>
<?php
}
