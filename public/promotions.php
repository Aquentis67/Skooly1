<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_promotion.php';

$pdo = pdo();
[$tri, $ordre] = lire_tri(COLONNES_TRI_PROMOTIONS, 'code');
[$lignes, $pagination] = lister_promotions($pdo, $tri, $ordre, lire_page());

entete('Promotions', 'promotions');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Promotions</h1>
    <a class="btn btn-primary" href="promotion_form.php">+ Nouvelle promotion</a>
</div>
<div class="mb-2"><?= filtre_instantane('table-promotions') ?></div>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="table-promotions">
    <thead class="table-light"><tr>
        <?= th_tri('Code', 'code', $tri, $ordre) ?>
        <?= th_tri('Libellé', 'libelle', $tri, $ordre) ?>
        <?= th_tri('Début', 'debut', $tri, $ordre) ?>
        <?= th_tri('Fin', 'fin', $tri, $ordre) ?>
        <?= th_tri('Apprenants', 'effectif', $tri, $ordre) ?>
        <th scope="col"><span class="visually-hidden">Actions</span></th>
    </tr></thead>
    <tbody>
    <?php if (!$lignes) : ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Aucune promotion.</td></tr>
    <?php endif; ?>
    <?php foreach ($lignes as $p) : ?>
        <tr>
            <td><a href="promotion.php?id=<?= (int) $p['id'] ?>"><strong><?= e($p['code']) ?></strong></a></td>
            <td><?= e($p['libelle']) ?></td>
            <td><?= e(date_fr($p['date_debut'])) ?></td>
            <td><?= e(date_fr($p['date_fin'])) ?></td>
            <td><?= (int) $p['nb_apprenants'] ?></td>
            <td class="text-nowrap text-end">
                <a class="btn btn-sm btn-outline-secondary" href="promotion.php?id=<?= (int) $p['id'] ?>">Détail</a>
                <a class="btn btn-sm btn-outline-primary" href="promotion_form.php?id=<?= (int) $p['id'] ?>">Modifier</a>
                <a class="btn btn-sm btn-outline-danger" href="promotion_supprimer.php?id=<?= (int) $p['id'] ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php afficher_pagination($pagination); ?>
<?php pied(); ?>
