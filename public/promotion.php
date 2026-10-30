<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_promotion.php';
require __DIR__ . '/../includes/fonctions_apprenant.php';
require __DIR__ . '/../includes/fonctions_intervention.php';

$pdo = pdo();
$id = param_id();
$promotion = $id ? trouver_promotion($pdo, $id) : null;
if ($promotion === null) {
    flash_ajouter('danger', 'Promotion introuvable.');
    rediriger('promotions.php');
}

// Deux listes sur la meme page : chacune a ses parametres (a_tri, i_tri, a_page...)
[$tri_a, $ordre_a] = lire_tri(COLONNES_TRI_APPRENANTS, 'nom', 'a_');
[$apprenants, $pag_a] = lister_apprenants($pdo, ['promotion_id' => $id], $tri_a, $ordre_a, lire_page('a_'), 10);
[$tri_i, $ordre_i] = lire_tri(COLONNES_TRI_INTERVENTIONS, 'matiere', 'i_');
[$interventions, $pag_i] = lister_interventions($pdo, ['promotion_id' => $id], $tri_i, $ordre_i, lire_page('i_'), 10);
$total_heures = total_heures_interventions($pdo, ['promotion_id' => $id]);
$volumes = volumes_par_matiere($pdo, $id);

entete('Promotion ' . $promotion['code'], 'promotions');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h3 mb-0"><?= e($promotion['code']) ?> <small class="text-muted fs-6"><?= e($promotion['libelle']) ?></small></h1>
        <div class="text-muted">Du <?= e(date_fr($promotion['date_debut'])) ?> au <?= e(date_fr($promotion['date_fin'])) ?></div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="promotion_form.php?id=<?= (int) $id ?>">Modifier</a>
        <a class="btn btn-outline-secondary" href="promotions.php">Toutes les promotions</a>
    </div>
</div>

<h2 class="h5 mt-4">Apprenants <span class="badge text-bg-secondary"><?= (int) $pag_a['total'] ?></span></h2>
<div class="mb-2 d-flex gap-2 flex-wrap">
    <a class="btn btn-sm btn-primary" href="apprenant_form.php?promotion=<?= (int) $id ?>">+ Ajouter un apprenant</a>
    <a class="btn btn-sm btn-outline-secondary" href="apprenants.php?promotion=<?= (int) $id ?>">Ouvrir dans la liste complète (recherche, filtres)</a>
</div>
<?php afficher_tableau_apprenants($apprenants, $tri_a, $ordre_a, 'a_', 'table-apprenants-promo'); ?>
<?php afficher_pagination($pag_a, 'a_'); ?>

<h2 class="h5 mt-4">Interventions <span class="badge text-bg-secondary"><?= (int) $pag_i['total'] ?></span></h2>
<p class="mb-2"><strong>Total d'heures prévues : <?= e(heures($total_heures)) ?></strong>
   <a class="btn btn-sm btn-primary ms-3" href="intervention_form.php?promotion_id=<?= (int) $id ?>">+ Ajouter une intervention</a></p>
<?php afficher_tableau_interventions($interventions, $tri_i, $ordre_i, 'i_', ['promotion'], 'table-interventions-promo'); ?>
<?php afficher_pagination($pag_i, 'i_'); ?>

<?php if ($volumes) : ?>
    <h2 class="h6 mt-4">Volume par matière (somme des interventions)</h2>
    <ul class="list-inline">
    <?php foreach ($volumes as $v) : ?>
        <li class="list-inline-item badge text-bg-light border"><?= e($v['libelle']) ?> : <?= e(heures($v['total'])) ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php pied(); ?>
