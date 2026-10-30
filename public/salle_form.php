<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_salle.php';

$pdo = pdo();
$salle = null;
if (array_key_exists('id', $_GET)) {
    $id = param_id();
    $salle = $id ? trouver_salle($pdo, $id) : null;
    if ($salle === null) {
        flash_ajouter('danger', 'Salle introuvable.');
        rediriger('salles.php');
    }
}
$champs = ['numero', 'libelle', 'capacite', 'batiment', 'remarques'];
$erreurs = [];

if (requete_post()) {
    csrf_verifier();
    $valeurs = lire_post($champs);
    $valeurs['videoprojecteur'] = isset($_POST['videoprojecteur']) ? '1' : '';   // case a cocher : oui/non
    $erreurs = valider_salle($pdo, $valeurs, $salle ? (int) $salle['id'] : null);
    if (!$erreurs) {
        if ($salle) {
            modifier_salle($pdo, (int) $salle['id'], $valeurs);
            flash_ajouter('success', "La salle {$valeurs['numero']} a été modifiée.");
        } else {
            creer_salle($pdo, $valeurs);
            flash_ajouter('success', "La salle {$valeurs['numero']} a été créée.");
        }
        rediriger('salles.php');
    }
} elseif ($salle) {
    $valeurs = ['numero' => $salle['numero'], 'libelle' => $salle['libelle'], 'capacite' => (string) $salle['capacite'],
                'batiment' => $salle['batiment'], 'videoprojecteur' => $salle['videoprojecteur'] ? '1' : '',
                'remarques' => (string) ($salle['remarques'] ?? '')];
} else {
    $valeurs = array_fill_keys($champs, '');
    $valeurs['videoprojecteur'] = '';
}

$titre = $salle ? 'Modifier une salle' : 'Nouvelle salle';
$action = 'salle_form.php' . ($salle ? '?id=' . (int) $salle['id'] : '');
entete($titre, 'salles');
?>
<h1 class="h3 mb-3"><?= e($titre) ?></h1>
<?= bloc_erreurs($erreurs) ?>
<?php carte_debut(); ?>
<form method="post" action="<?= e($action) ?>" novalidate>
    <?= csrf_champ() ?>
    <?= note_champs_obligatoires() ?>
    <div class="row">
        <div class="col-md-4"><?= champ_texte('numero', 'Numéro', $valeurs, $erreurs, ['maxlength' => 10, 'placeholder' => '101']) ?></div>
        <div class="col-md-8"><?= champ_texte('libelle', 'Libellé', $valeurs, $erreurs, ['maxlength' => 60]) ?></div>
        <div class="col-md-4"><?= champ_texte('capacite', 'Capacité (places)', $valeurs, $erreurs, ['maxlength' => 4, 'type' => 'text']) ?></div>
        <div class="col-md-4"><?= champ_texte('batiment', 'Bâtiment', $valeurs, $erreurs, ['maxlength' => 40]) ?></div>
        <div class="col-md-4 pt-md-4"><?= champ_case('videoprojecteur', 'Équipée d’un vidéoprojecteur', $valeurs, $erreurs) ?></div>
        <div class="col-12"><?= champ_zone('remarques', 'Remarques', $valeurs, $erreurs,
            ['maxlength' => 500, 'aide' => 'Facultatif : état du matériel, particularités d’accès, etc. (500 caractères maximum)']) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="salles.php">Annuler</a>
    </div>
</form>
<?php carte_fin(); ?>
<?php pied(); ?>
