<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_intervenant.php';

$pdo = pdo();
$intervenant = null;
if (array_key_exists('id', $_GET)) {
    $id = param_id();
    $intervenant = $id ? trouver_intervenant($pdo, $id) : null;
    if ($intervenant === null) {
        flash_ajouter('danger', 'Intervenant introuvable.');
        rediriger('intervenants.php');
    }
}
$champs = ['nom', 'prenom', 'email', 'telephone', 'statut', 'tarif_horaire'];
$erreurs = [];

if (requete_post()) {
    csrf_verifier();
    $valeurs = lire_post($champs);
    $erreurs = valider_intervenant($pdo, $valeurs, $intervenant ? (int) $intervenant['id'] : null);
    if (!$erreurs) {
        $donnees = preparer_intervenant($valeurs);
        if ($intervenant) {
            modifier_intervenant($pdo, (int) $intervenant['id'], $donnees);
            flash_ajouter('success', "L'intervenant {$donnees['prenom']} {$donnees['nom']} a été modifié.");
        } else {
            creer_intervenant($pdo, $donnees);
            flash_ajouter('success', "L'intervenant {$donnees['prenom']} {$donnees['nom']} a été créé.");
        }
        rediriger('intervenants.php');
    }
} elseif ($intervenant) {
    $valeurs = array_fill_keys($champs, '');
    foreach ($champs as $c) {
        $valeurs[$c] = (string) ($intervenant[$c] ?? '');
    }
    $valeurs['tarif_horaire'] = $intervenant['tarif_horaire'] === null ? '' : str_replace('.', ',', $intervenant['tarif_horaire']);
} else {
    $valeurs = array_fill_keys($champs, '');
}

$titre = $intervenant ? 'Modifier un intervenant' : 'Nouvel intervenant';
$action = 'intervenant_form.php' . ($intervenant ? '?id=' . (int) $intervenant['id'] : '');
entete($titre, 'intervenants');
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
        <div class="col-md-6"><?= champ_texte('email', 'Adresse électronique', $valeurs, $erreurs, ['type' => 'email', 'regle' => 'email', 'maxlength' => 150]) ?></div>
        <div class="col-md-6"><?= champ_texte('telephone', 'Téléphone', $valeurs, $erreurs,
            ['obligatoire' => false, 'type' => 'tel', 'regle' => 'telephone', 'placeholder' => '06 12 34 56 78', 'maxlength' => 20]) ?></div>
        <div class="col-md-6"><?= champ_select('statut', 'Statut', STATUTS_INTERVENANT, $valeurs, $erreurs) ?></div>
        <div class="col-md-6"><?= champ_texte('tarif_horaire', 'Tarif horaire (€)', $valeurs, $erreurs,
            ['obligatoire' => false, 'maxlength' => 7, 'placeholder' => '45,00', 'aide' => 'Obligatoire pour un vacataire, interdit pour un salarié.']) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a class="btn btn-outline-secondary" href="intervenants.php">Annuler</a>
    </div>
</form>
<?php carte_fin(); ?>
<?php pied(); ?>
