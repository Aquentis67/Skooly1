<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_intervenant.php';

$pdo = pdo();
$id = param_id();
$intervenant = $id ? trouver_intervenant($pdo, $id) : null;
if ($intervenant === null) {
    flash_ajouter('danger', 'Intervenant introuvable.');
    rediriger('intervenants.php');
}
$blocage = blocage_suppression_intervenant($pdo, $intervenant);   // RG09

if (requete_post()) {
    csrf_verifier();
    if ($blocage !== null) {
        flash_ajouter('danger', $blocage);
        rediriger('intervenants.php');
    }
    try {
        supprimer_intervenant($pdo, (int) $intervenant['id']);
        flash_ajouter('success', "L'intervenant {$intervenant['prenom']} {$intervenant['nom']} a été supprimé.");
    } catch (PDOException $ex) {
        if ($ex->getCode() !== '23000') {
            throw $ex;
        }
        flash_ajouter('danger', 'Suppression refusée : des interventions sont rattachées à cet intervenant.');
    }
    rediriger('intervenants.php');
}

entete('Supprimer un intervenant', 'intervenants');
?>
<h1 class="h3 mb-3">Supprimer un intervenant</h1>
<?php if ($blocage !== null) : ?>
    <div class="alert alert-danger" role="alert"><?= e($blocage) ?></div>
    <a class="btn btn-outline-secondary" href="intervenants.php">Retour aux intervenants</a>
<?php else : ?>
    <?php carte_debut('carte-danger'); ?>
        <p>Vous allez supprimer <strong>définitivement</strong> l'intervenant
           <strong><?= e($intervenant['prenom'] . ' ' . $intervenant['nom']) ?></strong>
           (<?= e(STATUTS_INTERVENANT[$intervenant['statut']]) ?>, <?= e($intervenant['email']) ?>).</p>
        <form method="post" action="intervenant_supprimer.php?id=<?= (int) $intervenant['id'] ?>" class="d-flex gap-2">
            <?= csrf_champ() ?>
            <button class="btn btn-danger" type="submit">Oui, supprimer cet intervenant</button>
            <a class="btn btn-outline-secondary" href="intervenants.php">Annuler</a>
        </form>
<?php carte_fin(); ?>
<?php endif; ?>
<?php pied(); ?>
