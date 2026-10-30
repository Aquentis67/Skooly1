<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_salle.php';

$pdo = pdo();
[$tri, $ordre] = lire_tri(COLONNES_TRI_SALLES, 'numero');
[$lignes, $pagination] = lister_salles($pdo, $tri, $ordre, lire_page());

entete('Salles', 'salles');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Salles</h1>
    <a class="btn btn-primary" href="salle_form.php">+ Nouvelle salle</a>
</div>
<div class="mb-2"><?= filtre_instantane('table-salles') ?></div>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="table-salles">
    <thead class="table-light"><tr>
        <?= th_tri('Numéro', 'numero', $tri, $ordre) ?>
        <?= th_tri('Libellé', 'libelle', $tri, $ordre) ?>
        <?= th_tri('Capacité', 'capacite', $tri, $ordre) ?>
        <?= th_tri('Bâtiment', 'batiment', $tri, $ordre) ?>
        <?= th_tri('Vidéoprojecteur', 'videoprojecteur', $tri, $ordre) ?>
        <?= th_tri('Remarques', 'remarques', $tri, $ordre) ?>
        <th scope="col"><span class="visually-hidden">Actions</span></th>
    </tr></thead>
    <tbody>
    <?php if (!$lignes) : ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Aucune salle.</td></tr>
    <?php endif; ?>
    <?php foreach ($lignes as $s) : ?>
        <tr>
            <td><strong><?= e($s['numero']) ?></strong></td>
            <td><?= e($s['libelle']) ?></td>
            <td><?= (int) $s['capacite'] ?> places</td>
            <td><?= e($s['batiment']) ?></td>
            <td><?= $s['videoprojecteur'] ? '<span class="badge text-bg-success">Oui</span>' : '<span class="badge text-bg-secondary">Non</span>' ?></td>
            <td class="text-break"><?= $s['remarques'] === null ? '<span class="text-muted">—</span>' : e($s['remarques']) ?></td>
            <?= boutons_actions('salle_form.php', 'salle_supprimer.php', (int) $s['id']) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php afficher_pagination($pagination); ?>
<?php pied(); ?>
