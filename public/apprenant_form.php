<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_promotion.php';
require __DIR__ . '/../includes/fonctions_apprenant.php';

$pdo = pdo();
$apprenant = null;
if (array_key_exists('id', $_GET)) {            // modification
    $id = param_id();
    $apprenant = $id ? trouver_apprenant($pdo, $id) : null;
    if ($apprenant === null) {
        flash_ajouter('danger', 'Apprenant introuvable.');
        rediriger('apprenants.php');
    }
}
$promotions = promotions_pour_select($pdo);
$champs = ['nom', 'prenom', 'date_naissance', 'email', 'telephone', 'regime', 'entreprise', 'statut', 'promotion_id'];
$erreurs = [];

if (requete_post()) {
    csrf_verifier();
    $valeurs = lire_post($champs);
    $erreurs = valider_apprenant($valeurs, $promotions);
    if (!$erreurs) {
        $donnees = preparer_apprenant($valeurs);
        if ($apprenant) {
            modifier_apprenant($pdo, (int) $apprenant['id'], $donnees);
            flash_ajouter('success', "L'apprenant {$donnees['prenom']} {$donnees['nom']} a été modifié.");
        } else {
            creer_apprenant($pdo, $donnees);
            flash_ajouter('success', "L'apprenant {$donnees['prenom']} {$donnees['nom']} a été créé.");
        }
        rediriger('apprenants.php');
    }
} elseif ($apprenant) {
    $valeurs = array_fill_keys($champs, '');
    foreach ($champs as $c) {
        $valeurs[$c] = (string) ($apprenant[$c] ?? '');
    }
    $valeurs['date_naissance'] = date_fr($apprenant['date_naissance']);
} else {
    $valeurs = array_fill_keys($champs, '');
    $valeurs['regime'] = 'initial';
    $valeurs['statut'] = 'inscrit';
    if (param_id('promotion') !== null) {
        $valeurs['promotion_id'] = (string) param_id('promotion');
    }
}

$titre = $apprenant ? 'Modifier un apprenant' : 'Nouvel apprenant';
$action = 'apprenant_form.php' . ($apprenant ? '?id=' . (int) $apprenant['id'] : '');
entete($titre, 'apprenants');
?>
<h1 class="h3 mb-3"><?= e($titre) ?></h1>
<?= bloc_erreurs($erreurs) ?>
<?php carte_debut(); ?>
<form method="post" action="<?= e($action) ?>" novalidate>
    <?= csrf_champ() ?>
    <?= note_champs_obligatoires() ?>
    <div class="row">
        <div class="col-md-6"><?= champ_texte('nom', 'Nom', $valeurs, $erreurs, ['maxlength' => 60]) ?></div>
        <div class="col-md-6"><?= champ_texte('prenom', 'Prénom', $valeurs, $erreurs, ['maxlength' => 60]) ?></div>
        <div class="col-md-6"><?= champ_texte('date_naissance', 'Date de naissance', $valeurs, $erreurs,
            ['placeholder' => 'JJ/MM/AAAA', 'regle' => 'date', 'maxlength' => 10]) ?></div>
        <div class="col-md-6"><?= champ_select('promotion_id', 'Promotion', $promotions, $valeurs, $erreurs) ?></div>
        <div class="col-md-6"><?= champ_texte('email', 'Adresse électronique', $valeurs, $erreurs,
            ['type' => 'email', 'regle' => 'email', 'maxlength' => 150]) ?></div>
        <div class="col-md-6"><?= champ_texte('telephone', 'Téléphone', $valeurs, $erreurs,
            ['obligatoire' => false, 'type' => 'tel', 'regle' => 'telephone', 'placeholder' => '06 12 34 56 78', 'maxlength' => 20]) ?></div>
        <div class="col-md-4"><?= champ_select('regime', 'Régime', REGIMES, $valeurs, $erreurs) ?></div>
        <div class="col-md-4"><?= champ_texte('entreprise', "Entreprise d'accueil", $valeurs, $erreurs,
            ['obligatoire' => false, 'maxlength' => 100, 'aide' => 'Obligatoire pour un alternant.']) ?></div>
        <div class="col-md-4"><?= champ_select('statut', 'Statut de scolarité', STATUTS_SCOLARITE, $valeurs, $erreurs) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="apprenants.php">Annuler</a>
    </div>
</form>
<?php carte_fin(); ?>
<?php pied(); ?>
