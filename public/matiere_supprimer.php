<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_matiere.php';

$pdo = pdo();
$id = param_id();
$matiere = $id ? trouver_matiere($pdo, $id) : null;
if ($matiere === null) {
    flash_ajouter('danger', 'Matière introuvable.');
    rediriger('matieres.php');
}
$blocage = blocage_suppression_matiere($pdo, $matiere);   // RG09
$nb_enseignants = compter_enseignants_matiere($pdo, (int) $matiere['id']);

if (requete_post()) {
    csrf_verifier();
    if ($blocage !== null) {
        flash_ajouter('danger', $blocage);
        rediriger('matieres.php');
    }
    try {
        supprimer_matiere($pdo, (int) $matiere['id']);
        flash_ajouter('success', "La matière {$matiere['libelle']} a été supprimée.");
    } catch (PDOException $ex) {
        if ($ex->getCode() !== '23000') {
            throw $ex;
        }
        flash_ajouter('danger', 'Suppression refusée : des interventions utilisent cette matière.');
    }
    rediriger('matieres.php');
}

entete('Supprimer une matière', 'matieres');
?>
<h1 class="h3 mb-3">Supprimer une matière</h1>
<?php if ($blocage !== null) : ?>
    <div class="alert alert-danger" role="alert"><?= e($blocage) ?></div>
    <a class="btn btn-outline-secondary" href="matieres.php">Retour aux matières</a>
<?php else : ?>
    <?php carte_debut('carte-danger'); ?>
        <p>Vous allez supprimer <strong>définitivement</strong> la matière
           <strong><?= e($matiere['libelle']) ?></strong> (code <?= e($matiere['code']) ?>).</p>
        <?php if ($nb_enseignants > 0) : ?><p class="text-muted"><?= (int) $nb_enseignants ?> intervenant(s) sont déclarés enseigner cette matière : ce lien sera retiré.</p><?php endif; ?>
        <form method="post" action="matiere_supprimer.php?id=<?= (int) $matiere['id'] ?>" class="d-flex gap-2">
            <?= csrf_champ() ?>
            <button class="btn btn-danger" type="submit">Oui, supprimer cette matière</button>
            <a class="btn btn-outline-secondary" href="matieres.php">Annuler</a>
        </form>
<?php carte_fin(); ?>
<?php endif; ?>
<?php pied(); ?>
