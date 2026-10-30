<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_accueil.php';

$n = compter_ensembles(pdo());
$cartes = [
    ['Apprenants', $n['apprenants'], 'apprenants.php'],
    ['Promotions', $n['promotions'], 'promotions.php'],
    ['Intervenants', $n['intervenants'], 'intervenants.php'],
    ['Matières', $n['matieres'], 'matieres.php'],
    ['Salles', $n['salles'], 'salles.php'],
    ['Interventions', $n['interventions'], 'interventions.php'],
];
entete('Tableau de bord', 'accueil');
?>
<h1 class="h3 mb-4">Tableau de bord</h1>
<div class="row g-3">
<?php foreach ($cartes as [$titre, $nombre, $url]) : ?>
    <div class="col-6 col-md-4">
        <a href="<?= e($url) ?>" class="lien-carte">
            <?php carte_debut('carte-compteur h-100', 3); ?>
                <div class="nombre"><?= (int) $nombre ?></div>
                <div><?= e($titre) ?></div>
            <?php carte_fin(); ?>
        </a>
    </div>
<?php endforeach; ?>
</div>
<?php pied(); ?>
