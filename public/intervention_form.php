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
$champs = ['intervenant_id', 'matiere_id', 'promotion_id', 'volume_horaire'];
$erreurs = [];

if (requete_post()) {
    csrf_verifier();
    $valeurs = lire_post($champs);
    $erreurs = valider_intervention($pdo, $valeurs, $intervenants, $matieres, $promotions);
    if (!$erreurs) {
        $volume = number_format((float) str_replace(',', '.', $valeurs['volume_horaire']), 2, '.', '');
        try {
            creer_intervention($pdo, (int) $valeurs['intervenant_id'], (int) $valeurs['matiere_id'], (int) $valeurs['promotion_id'], $volume);
            flash_ajouter('success', "L'intervention a été enregistrée ({$intervenants[(int) $valeurs['intervenant_id']]}, "
                . "{$matieres[(int) $valeurs['matiere_id']]}, " . heures($volume) . ').');
            rediriger('interventions.php?promotion=' . (int) $valeurs['promotion_id']);
        } catch (PDOException $ex) {
            if ($ex->getCode() !== '23000') {   // doublon pose entre la verification et l'insertion
                throw $ex;
            }
            $erreurs['matiere_id'] = 'Cette intervention existe déjà (même intervenant, même matière, même promotion).';
        }
    }
} else {
    $valeurs = array_fill_keys($champs, '');
    foreach (['intervenant_id', 'matiere_id', 'promotion_id'] as $c) {      // pre-remplissage depuis une fiche
        $valeurs[$c] = param_id($c) !== null ? (string) param_id($c) : '';
    }
}

entete('Nouvelle intervention', 'interventions');
?>
<h1 class="h3 mb-3">Nouvelle intervention</h1>
<?= bloc_erreurs($erreurs) ?>
<?php carte_debut(); ?>
<form method="post" action="intervention_form.php" novalidate>
    <?= csrf_champ() ?>
    <?= note_champs_obligatoires() ?>
    <div class="row">
        <div class="col-md-6"><?= champ_select('intervenant_id', 'Intervenant', $intervenants, $valeurs, $erreurs) ?></div>
        <div class="col-md-6"><?= champ_select('matiere_id', 'Matière', $matieres, $valeurs, $erreurs) ?></div>
        <div class="col-md-6"><?= champ_select('promotion_id', 'Promotion', $promotions, $valeurs, $erreurs) ?></div>
        <div class="col-md-6"><?= champ_texte('volume_horaire', 'Volume horaire (heures)', $valeurs, $erreurs,
            ['maxlength' => 7, 'placeholder' => '40', 'aide' => 'Heures attribuées à cet intervenant pour cette matière dans cette promotion.']) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="interventions.php">Annuler</a>
    </div>
</form>
<?php carte_fin(); ?>
<?php pied(); ?>
