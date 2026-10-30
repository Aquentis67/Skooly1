<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_salle.php';

$pdo = pdo();
$id = param_id();
$salle = $id ? trouver_salle($pdo, $id) : null;
if ($salle === null) {
    flash_ajouter('danger', 'Salle introuvable.');
    rediriger('salles.php');
}

if (requete_post()) {
    csrf_verifier();
    supprimer_salle($pdo, (int) $salle['id']);
    flash_ajouter('success', "La salle {$salle['numero']} a été supprimée.");
    rediriger('salles.php');
}

entete('Supprimer une salle', 'salles');
?>
<h1 class="h3 mb-3">Supprimer une salle</h1>
<?php carte_debut('carte-danger'); ?>
    <p>Vous allez supprimer <strong>définitivement</strong> la salle
       <strong><?= e($salle['numero']) ?> — <?= e($salle['libelle']) ?></strong>
       (<?= (int) $salle['capacite'] ?> places, <?= e($salle['batiment']) ?>).</p>
    <form method="post" action="salle_supprimer.php?id=<?= (int) $salle['id'] ?>" class="d-flex gap-2">
        <?= csrf_champ() ?>
        <button class="btn btn-danger" type="submit">Oui, supprimer cette salle</button>
        <a class="btn btn-outline-secondary" href="salles.php">Annuler</a>
    </form>
<?php carte_fin(); ?>
<?php pied(); ?>
