<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_apprenant.php';

$pdo = pdo();
$id = param_id();
$apprenant = $id ? trouver_apprenant($pdo, $id) : null;
if ($apprenant === null) {
    flash_ajouter('danger', 'Apprenant introuvable.');
    rediriger('apprenants.php');
}

if (requete_post()) {
    csrf_verifier();
    supprimer_apprenant($pdo, (int) $apprenant['id']);
    flash_ajouter('success', "L'apprenant {$apprenant['prenom']} {$apprenant['nom']} a été supprimé.");
    rediriger('apprenants.php');
}

entete('Supprimer un apprenant', 'apprenants');
?>
<h1 class="h3 mb-3">Supprimer un apprenant</h1>
<?php carte_debut('carte-danger'); ?>
        <p class="mb-2">Vous allez supprimer <strong>définitivement</strong> l'apprenant suivant :</p>
        <ul>
            <li><strong><?= e($apprenant['prenom'] . ' ' . $apprenant['nom']) ?></strong></li>
            <li>Promotion : <?= e($apprenant['promotion_code']) ?></li>
            <li>E-mail : <?= e($apprenant['email']) ?></li>
            <li>Né(e) le <?= e(date_fr($apprenant['date_naissance'])) ?></li>
        </ul>
        <form method="post" action="apprenant_supprimer.php?id=<?= (int) $apprenant['id'] ?>" class="d-flex gap-2">
            <?= csrf_champ() ?>
            <button class="btn btn-danger" type="submit">Oui, supprimer cet apprenant</button>
            <a class="btn btn-outline-secondary" href="apprenants.php">Annuler</a>
        </form>
<?php carte_fin(); ?>
<?php pied(); ?>
