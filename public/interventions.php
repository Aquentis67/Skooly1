<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_promotion.php';
require __DIR__ . '/../includes/fonctions_intervenant.php';
require __DIR__ . '/../includes/fonctions_matiere.php';
require __DIR__ . '/../includes/fonctions_intervention.php';

$pdo = pdo();
$promotions = promotions_pour_select($pdo);
$intervenants = intervenants_pour_select($pdo);
$matieres = matieres_pour_select($pdo);

$choix = function (string $cle, array $existants): ?int {
    $n = param_id($cle);
    return ($n !== null && isset($existants[$n])) ? $n : null;
};
$filtres = [
    'promotion_id'   => $choix('promotion', $promotions),
    'intervenant_id' => $choix('intervenant', $intervenants),
    'matiere_id'     => $choix('matiere', $matieres),
];
[$tri, $ordre] = lire_tri(COLONNES_TRI_INTERVENTIONS, 'promotion');
[$lignes, $pagination] = lister_interventions($pdo, $filtres, $tri, $ordre, lire_page());
$total_heures = total_heures_interventions($pdo, $filtres);

$liste = function (string $nom, string $libelle, array $options, ?int $courant): void {
    echo '<div class="col-md-3"><label for="f-' . e($nom) . '" class="form-label mb-1">' . e($libelle) . '</label>'
       . '<select class="form-select" id="f-' . e($nom) . '" name="' . e($nom) . '"><option value="">Tous</option>';
    foreach ($options as $id => $texte) {
        echo '<option value="' . (int) $id . '"' . ($id === $courant ? ' selected' : '') . '>' . e($texte) . '</option>';
    }
    echo '</select></div>';
};

entete('Interventions', 'interventions');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Interventions</h1>
    <a class="btn btn-primary" href="intervention_form.php<?= $filtres['promotion_id'] ? '?promotion_id=' . (int) $filtres['promotion_id'] : '' ?>">+ Nouvelle intervention</a>
</div>
<p class="text-muted">Une intervention lie un intervenant, une matière et une promotion, avec un volume horaire prévu.
   (Ce n’est pas une séance planifiée : ni date, ni salle.)</p>

<?php carte_debut(); ?>
<form method="get" action="interventions.php">
    <div class="row g-2 align-items-end">
        <?php $liste('promotion', 'Promotion', $promotions, $filtres['promotion_id']); ?>
        <?php $liste('intervenant', 'Intervenant', $intervenants, $filtres['intervenant_id']); ?>
        <?php $liste('matiere', 'Matière', $matieres, $filtres['matiere_id']); ?>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filtrer</button>
            <a class="btn btn-outline-secondary" href="interventions.php">Réinitialiser</a>
        </div>
    </div>
</form>
<?php carte_fin(); ?>

<p class="mb-2"><strong>Total d'heures prévues (sélection) : <?= e(heures($total_heures)) ?></strong></p>
<div class="mb-2"><?= filtre_instantane('table-interventions') ?></div>
<?php afficher_tableau_interventions($lignes, $tri, $ordre); ?>
<?php afficher_pagination($pagination); ?>
<?php pied(); ?>
