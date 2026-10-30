<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_matiere.php';

$pdo = pdo();
$matiere = null;
if (array_key_exists('id', $_GET)) {
    $id = param_id();
    $matiere = $id ? trouver_matiere($pdo, $id) : null;
    if ($matiere === null) {
        flash_ajouter('danger', 'Matière introuvable.');
        rediriger('matieres.php');
    }
}
$champs = ['code', 'libelle'];
$erreurs = [];

if (requete_post()) {
    csrf_verifier();
    $valeurs = lire_post($champs);
    $erreurs = valider_matiere($pdo, $valeurs, $matiere ? (int) $matiere['id'] : null);
    if (!$erreurs) {
        if ($matiere) {
            modifier_matiere($pdo, (int) $matiere['id'], $valeurs);
            flash_ajouter('success', "La matière {$valeurs['libelle']} a été modifiée.");
        } else {
            creer_matiere($pdo, $valeurs);
            flash_ajouter('success', "La matière {$valeurs['libelle']} a été créée.");
        }
        rediriger('matieres.php');
    }
} elseif ($matiere) {
    $valeurs = ['code' => $matiere['code'], 'libelle' => $matiere['libelle']];
} else {
    $valeurs = array_fill_keys($champs, '');
}

$titre = $matiere ? 'Modifier une matière' : 'Nouvelle matière';
$action = 'matiere_form.php' . ($matiere ? '?id=' . (int) $matiere['id'] : '');
entete($titre, 'matieres');
?>
<h1 class="h3 mb-3"><?= e($titre) ?></h1>
<?= bloc_erreurs($erreurs) ?>
<?php carte_debut(); ?>
<form method="post" action="<?= e($action) ?>" novalidate>
    <?= csrf_champ() ?>
    <?= note_champs_obligatoires() ?>
    <div class="row">
        <div class="col-md-4"><?= champ_texte('code', 'Code', $valeurs, $erreurs, ['maxlength' => 20, 'placeholder' => 'COMPTA-GEN']) ?></div>
        <div class="col-md-8"><?= champ_texte('libelle', 'Libellé', $valeurs, $erreurs, ['maxlength' => 100]) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="matieres.php">Annuler</a>
    </div>
</form>
<?php carte_fin(); ?>
<?php pied(); ?>
