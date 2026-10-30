<?php
require __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/commun.php';
require __DIR__ . '/../includes/fonctions_intervenant.php';
require __DIR__ . '/../includes/fonctions_intervention.php';
require __DIR__ . '/../includes/fonctions_matiere.php';

$pdo = pdo();
$id = param_id();
$intervenant = $id ? trouver_intervenant($pdo, $id) : null;
if ($intervenant === null) {
    flash_ajouter('danger', 'Intervenant introuvable.');
    rediriger('intervenants.php');
}
[$tri, $ordre] = lire_tri(COLONNES_TRI_INTERVENTIONS, 'matiere');
[$lignes, $pagination] = lister_interventions($pdo, ['intervenant_id' => $id], $tri, $ordre, lire_page());
$total_heures = total_heures_interventions($pdo, ['intervenant_id' => $id]);
$matieres_enseignees = matieres_de_intervenant($pdo, $id);

entete($intervenant['prenom'] . ' ' . $intervenant['nom'], 'intervenants');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h3 mb-0"><?= e($intervenant['prenom'] . ' ' . $intervenant['nom']) ?></h1>
        <div class="text-muted">
            <?= e(STATUTS_INTERVENANT[$intervenant['statut']]) ?>
            <?= $intervenant['tarif_horaire'] !== null ? ' — ' . e(euros($intervenant['tarif_horaire'])) : '' ?>
            — <?= e($intervenant['email']) ?>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="intervenant_form.php?id=<?= (int) $id ?>">Modifier</a>
        <a class="btn btn-outline-secondary" href="intervenants.php">Tous les intervenants</a>
    </div>
</div>

<p class="mb-1"><strong>Matières enseignées :</strong>
<?php if (!$matieres_enseignees) : ?><span class="text-muted">aucune</span><?php endif; ?>
<?php foreach ($matieres_enseignees as $m) : ?><span class="badge text-bg-light border"><?= e($m['libelle']) ?></span> <?php endforeach; ?></p>
<?php if ($intervenant['remarques'] !== null) : ?><p class="mb-3"><strong>Remarques :</strong> <?= e($intervenant['remarques']) ?></p><?php endif; ?>

<h2 class="h5">Interventions <span class="badge text-bg-secondary"><?= (int) $pagination['total'] ?></span></h2>
<p class="mb-2"><strong>Total d'heures : <?= e(heures($total_heures)) ?></strong>
   <a class="btn btn-sm btn-primary ms-3" href="intervention_form.php?intervenant_id=<?= (int) $id ?>">+ Ajouter une intervention</a></p>
<?php afficher_tableau_interventions($lignes, $tri, $ordre, '', ['intervenant'], 'table-interventions-intervenant'); ?>
<?php afficher_pagination($pagination); ?>
<?php pied(); ?>
