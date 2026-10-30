<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_promotion.php';

$pdo = pdo();
$id = param_id();
$promotion = $id ? trouver_promotion($pdo, $id) : null;
if ($promotion === null) {
    flash_ajouter('danger', 'Promotion introuvable.');
    rediriger('promotions.php');
}
$blocage = blocage_suppression_promotion($pdo, $promotion);   // RG09

if (requete_post()) {
    csrf_verifier();
    if ($blocage !== null) {
        flash_ajouter('danger', $blocage);
        rediriger('promotions.php');
    }
    try {
        supprimer_promotion($pdo, (int) $promotion['id']);
        flash_ajouter('success', "La promotion {$promotion['code']} a été supprimée.");
    } catch (PDOException $ex) {
        if ($ex->getCode() !== '23000') {      // 23000 = violation de cle etrangere
            throw $ex;
        }
        flash_ajouter('danger', "Suppression refusée : des données sont rattachées à la promotion {$promotion['code']}.");
    }
    rediriger('promotions.php');
}

entete('Supprimer une promotion', 'promotions');
?>
<h1 class="h3 mb-3">Supprimer une promotion</h1>
<?php if ($blocage !== null) : ?>
    <div class="alert alert-danger" role="alert"><?= e($blocage) ?></div>
    <a class="btn btn-outline-secondary" href="promotions.php">Retour aux promotions</a>
<?php else : ?>
    <?php carte_debut('carte-danger'); ?>
        <p>Vous allez supprimer <strong>définitivement</strong> la promotion
           <strong><?= e($promotion['code']) ?></strong> (<?= e($promotion['libelle']) ?>).</p>
        <form method="post" action="promotion_supprimer.php?id=<?= (int) $promotion['id'] ?>" class="d-flex gap-2">
            <?= csrf_champ() ?>
            <button class="btn btn-danger" type="submit">Oui, supprimer cette promotion</button>
            <a class="btn btn-outline-secondary" href="promotions.php">Annuler</a>
        </form>
<?php carte_fin(); ?>
<?php endif; ?>
<?php pied(); ?>
