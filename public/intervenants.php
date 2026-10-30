<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_intervenant.php';

$pdo = pdo();
$q = param_get('q');
$note_recherche = '';
if ($q !== '' && longueur($q) < 3) {
    $note_recherche = 'Saisissez au moins 3 lettres pour lancer la recherche.';
    $q = '';
}
$statut = param_get('statut');
$statut = array_key_exists($statut, STATUTS_INTERVENANT) ? $statut : '';

[$tri, $ordre] = lire_tri(COLONNES_TRI_INTERVENANTS, 'nom');
[$lignes, $pagination] = lister_intervenants($pdo, ['q' => $q, 'statut' => $statut], $tri, $ordre, lire_page());

entete('Intervenants', 'intervenants');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Intervenants</h1>
    <a class="btn btn-primary" href="intervenant_form.php">+ Nouvel intervenant</a>
</div>

<?php carte_debut(); ?>
<form method="get" action="intervenants.php">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="f-q" class="form-label mb-1">Recherche (nom, prénom, e-mail)</label>
            <input class="form-control" id="f-q" name="q" value="<?= e(param_get('q')) ?>" placeholder="3 lettres minimum">
        </div>
        <div class="col-md-3">
            <label for="f-statut" class="form-label mb-1">Statut</label>
            <select class="form-select" id="f-statut" name="statut">
                <option value="">Tous</option>
                <?php foreach (STATUTS_INTERVENANT as $code => $texte) : ?>
                    <option value="<?= e($code) ?>"<?= $code === $statut ? ' selected' : '' ?>><?= e($texte) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filtrer</button>
            <a class="btn btn-outline-secondary" href="intervenants.php">Réinitialiser</a>
        </div>
    </div>
    <?php if (isset($_GET['tri'])) : ?>
        <input type="hidden" name="tri" value="<?= e($tri) ?>">
        <input type="hidden" name="ordre" value="<?= e(strtolower($ordre)) ?>">
    <?php endif; ?>
    <?php if ($note_recherche !== '') : ?><div class="form-text text-warning-emphasis mt-2"><?= e($note_recherche) ?></div><?php endif; ?>
</form>
<?php carte_fin(); ?>

<div class="mb-2"><?= filtre_instantane('table-intervenants') ?></div>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="table-intervenants">
    <thead class="table-light"><tr>
        <?= th_tri('Nom', 'nom', $tri, $ordre) ?>
        <?= th_tri('Prénom', 'prenom', $tri, $ordre) ?>
        <?= th_tri('E-mail', 'email', $tri, $ordre) ?>
        <?= th_tri('Statut', 'statut', $tri, $ordre) ?>
        <?= th_tri('Tarif horaire', 'tarif', $tri, $ordre) ?>
        <th scope="col">Matières enseignées</th>
        <th scope="col"><span class="visually-hidden">Actions</span></th>
    </tr></thead>
    <tbody>
    <?php if (!$lignes) : ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Aucun intervenant ne correspond.</td></tr>
    <?php endif; ?>
    <?php foreach ($lignes as $i) : ?>
        <tr>
            <td><a href="intervenant.php?id=<?= (int) $i['id'] ?>"><?= e($i['nom']) ?></a></td>
            <td><?= e($i['prenom']) ?></td>
            <td><?= e($i['email']) ?></td>
            <td><span class="badge <?= $i['statut'] === 'vacataire' ? 'text-bg-warning' : 'text-bg-primary' ?>"><?= e(STATUTS_INTERVENANT[$i['statut']]) ?></span></td>
            <td class="text-nowrap"><?= e(euros($i['tarif_horaire'])) ?></td>
            <td><?= $i['matieres'] === null ? '<span class="text-muted">—</span>' : e($i['matieres']) ?></td>
            <td class="text-nowrap text-end">
                <a class="btn btn-sm btn-outline-primary" href="intervenant_form.php?id=<?= (int) $i['id'] ?>">Modifier</a>
                <a class="btn btn-sm btn-outline-danger" href="intervenant_supprimer.php?id=<?= (int) $i['id'] ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php afficher_pagination($pagination); ?>
<?php pied(); ?>
