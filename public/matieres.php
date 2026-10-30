<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_matiere.php';

$pdo = pdo();
[$tri, $ordre] = lire_tri(COLONNES_TRI_MATIERES, 'code');
[$lignes, $pagination] = lister_matieres($pdo, $tri, $ordre, lire_page());

entete('Matières', 'matieres');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Matières</h1>
    <a class="btn btn-primary" href="matiere_form.php">+ Nouvelle matière</a>
</div>
<div class="mb-2"><?= filtre_instantane('table-matieres') ?></div>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="table-matieres">
    <thead class="table-light"><tr>
        <?= th_tri('Code', 'code', $tri, $ordre) ?>
        <?= th_tri('Libellé', 'libelle', $tri, $ordre) ?>
        <?= th_tri('Interventions', 'interventions', $tri, $ordre) ?>
        <th scope="col"><span class="visually-hidden">Actions</span></th>
    </tr></thead>
    <tbody>
    <?php if (!$lignes) : ?>
        <tr><td colspan="4" class="text-center text-muted py-4">Aucune matière.</td></tr>
    <?php endif; ?>
    <?php foreach ($lignes as $m) : ?>
        <tr>
            <td><strong><?= e($m['code']) ?></strong></td>
            <td><?= e($m['libelle']) ?></td>
            <td><a href="interventions.php?matiere=<?= (int) $m['id'] ?>"><?= (int) $m['nb_interventions'] ?></a></td>
            <?= boutons_actions('matiere_form.php', 'matiere_supprimer.php', (int) $m['id']) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php afficher_pagination($pagination); ?>
<?php pied(); ?>
