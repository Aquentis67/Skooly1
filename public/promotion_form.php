<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_promotion.php';

$pdo = pdo();
$promotion = null;
if (array_key_exists('id', $_GET)) {
    $id = param_id();
    $promotion = $id ? trouver_promotion($pdo, $id) : null;
    if ($promotion === null) {
        flash_ajouter('danger', 'Promotion introuvable.');
        rediriger('promotions.php');
    }
}
$champs = ['code', 'libelle', 'date_debut', 'date_fin'];
$erreurs = [];

if (requete_post()) {
    csrf_verifier();
    $valeurs = lire_post($champs);
    $erreurs = valider_promotion($pdo, $valeurs, $promotion ? (int) $promotion['id'] : null);
    if (!$erreurs) {
        $donnees = [
            'code' => $valeurs['code'], 'libelle' => $valeurs['libelle'],
            'date_debut' => date_vers_sql($valeurs['date_debut']), 'date_fin' => date_vers_sql($valeurs['date_fin']),
        ];
        if ($promotion) {
            modifier_promotion($pdo, (int) $promotion['id'], $donnees);
            flash_ajouter('success', "La promotion {$donnees['code']} a été modifiée.");
        } else {
            creer_promotion($pdo, $donnees);
            flash_ajouter('success', "La promotion {$donnees['code']} a été créée.");
        }
        rediriger('promotions.php');
    }
} elseif ($promotion) {
    $valeurs = ['code' => $promotion['code'], 'libelle' => $promotion['libelle'],
                'date_debut' => date_fr($promotion['date_debut']), 'date_fin' => date_fr($promotion['date_fin'])];
} else {
    $valeurs = array_fill_keys($champs, '');
}

$titre = $promotion ? 'Modifier une promotion' : 'Nouvelle promotion';
$action = 'promotion_form.php' . ($promotion ? '?id=' . (int) $promotion['id'] : '');
entete($titre, 'promotions');
?>
<h1 class="h3 mb-3"><?= e($titre) ?></h1>
<?= bloc_erreurs($erreurs) ?>
<?php carte_debut(); ?>
<form method="post" action="<?= e($action) ?>" novalidate>
    <?= csrf_champ() ?>
    <?= note_champs_obligatoires() ?>
    <div class="row">
        <div class="col-md-4"><?= champ_texte('code', 'Code', $valeurs, $erreurs, ['maxlength' => 10, 'placeholder' => 'DCG 1']) ?></div>
        <div class="col-md-8"><?= champ_texte('libelle', 'Libellé', $valeurs, $erreurs, ['maxlength' => 100]) ?></div>
        <div class="col-md-6"><?= champ_texte('date_debut', 'Date de début', $valeurs, $erreurs, ['placeholder' => 'JJ/MM/AAAA', 'regle' => 'date', 'maxlength' => 10]) ?></div>
        <div class="col-md-6"><?= champ_texte('date_fin', 'Date de fin', $valeurs, $erreurs, ['placeholder' => 'JJ/MM/AAAA', 'regle' => 'date', 'maxlength' => 10]) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="promotions.php">Annuler</a>
    </div>
</form>
<?php carte_fin(); ?>
<?php pied(); ?>
