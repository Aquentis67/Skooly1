<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_intervention.php';

$pdo = pdo();
$id = param_id();
$intervention = $id ? trouver_intervention($pdo, $id) : null;
if ($intervention === null) {
    flash_ajouter('danger', 'Intervention introuvable.');
    rediriger('interventions.php');
}

if (requete_post()) {
    csrf_verifier();
    supprimer_intervention($pdo, (int) $intervention['id']);
    flash_ajouter('success', 'L’intervention a été supprimée.');
    rediriger('interventions.php');
}

entete('Supprimer une intervention', 'interventions');
?>
<h1 class="h3 mb-3">Supprimer une intervention</h1>
<?php carte_debut('carte-danger'); ?>
    <p>Vous allez supprimer <strong>définitivement</strong> l'intervention suivante :</p>
    <ul>
        <li>Intervenant : <strong><?= e($intervention['intervenant_prenom'] . ' ' . $intervention['intervenant_nom']) ?></strong></li>
        <li>Matière : <?= e($intervention['matiere_libelle']) ?></li>
        <li>Promotion : <?= e($intervention['promotion_code']) ?></li>
        <li>Volume : <?= e(heures($intervention['volume_horaire'])) ?></li>
    </ul>
    <form method="post" action="intervention_supprimer.php?id=<?= (int) $intervention['id'] ?>" class="d-flex gap-2">
        <?= csrf_champ() ?>
        <button class="btn btn-danger" type="submit">Oui, supprimer cette intervention</button>
        <a class="btn btn-outline-secondary" href="interventions.php">Annuler</a>
    </form>
<?php carte_fin(); ?>
<?php pied(); ?>
