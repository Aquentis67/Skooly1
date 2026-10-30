<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_promotion.php';
require __DIR__ . '/../includes/fonctions_apprenant.php';

$pdo = pdo();
$promotions = promotions_pour_select($pdo);

// Filtres combinables : recherche texte + promotion + regime (+ statut)
$q = param_get('q');
$note_recherche = '';
if ($q !== '' && longueur($q) < 3) {
    $note_recherche = 'Saisissez au moins 3 lettres pour lancer la recherche.';
    $q = '';
}
$promotion_id = param_id('promotion');
if ($promotion_id !== null && !isset($promotions[$promotion_id])) {
    $promotion_id = null;
}
$regime = param_get('regime');
$regime = array_key_exists($regime, REGIMES) ? $regime : '';
$statut = param_get('statut');
$statut = array_key_exists($statut, STATUTS_SCOLARITE) ? $statut : '';
$filtres = ['q' => $q, 'promotion_id' => $promotion_id, 'regime' => $regime, 'statut' => $statut];

[$tri, $ordre] = lire_tri(COLONNES_TRI_APPRENANTS, 'nom');
[$lignes, $pagination] = lister_apprenants($pdo, $filtres, $tri, $ordre, lire_page());

entete('Apprenants', 'apprenants');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Apprenants</h1>
    <a class="btn btn-primary" href="apprenant_form.php">+ Nouvel apprenant</a>
</div>

<?php carte_debut(); ?>
<form method="get" action="apprenants.php">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="f-q" class="form-label mb-1">Recherche (nom, prénom, e-mail)</label>
            <input class="form-control" id="f-q" name="q" value="<?= e(param_get('q')) ?>" placeholder="3 lettres minimum">
        </div>
        <div class="col-md-3">
            <label for="f-promotion" class="form-label mb-1">Promotion</label>
            <select class="form-select" id="f-promotion" name="promotion">
                <option value="">Toutes</option>
                <?php foreach ($promotions as $pid => $texte) : ?>
                    <option value="<?= $pid ?>"<?= $pid === $promotion_id ? ' selected' : '' ?>><?= e($texte) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="f-regime" class="form-label mb-1">Régime</label>
            <select class="form-select" id="f-regime" name="regime">
                <option value="">Tous</option>
                <?php foreach (REGIMES as $code => $texte) : ?>
                    <option value="<?= e($code) ?>"<?= $code === $regime ? ' selected' : '' ?>><?= e($texte) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="f-statut" class="form-label mb-1">Statut</label>
            <select class="form-select" id="f-statut" name="statut">
                <option value="">Tous</option>
                <?php foreach (STATUTS_SCOLARITE as $code => $texte) : ?>
                    <option value="<?= e($code) ?>"<?= $code === $statut ? ' selected' : '' ?>><?= e($texte) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filtrer</button>
            <a class="btn btn-outline-secondary" href="apprenants.php">Réinitialiser</a>
        </div>
    </div>
    <?php if (isset($_GET['tri'])) : ?>
        <input type="hidden" name="tri" value="<?= e($tri) ?>">
        <input type="hidden" name="ordre" value="<?= e(strtolower($ordre)) ?>">
    <?php endif; ?>
    <?php if ($note_recherche !== '') : ?><div class="form-text text-warning-emphasis mt-2"><?= e($note_recherche) ?></div><?php endif; ?>
</form>
<?php carte_fin(); ?>

<div class="mb-2"><?= filtre_instantane('table-apprenants') ?></div>
<?php afficher_tableau_apprenants($lignes, $tri, $ordre); ?>
<?php afficher_pagination($pagination); ?>
<?php pied(); ?>
