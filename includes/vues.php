<?php
// Morceaux d'affichage reutilises par plusieurs pages (aucun SQL ici).

// Panneau dessine a la main (<wired-card>, bibliotheque wired-elements).
// Le contenu reste du HTML normal : les formulaires qu'il contient fonctionnent
// meme si le JavaScript est absent ou si le dessin echoue (le CSS donne alors un
// trait de repli). N'ajoutez PAS l'attribut fill : wired-elements 3.0.0-rc.6 est
// incompatible avec RoughJS 4.6 (fillPolygon) et le dessin planterait.
function carte_debut(string $classe = '', int $relief = 2): void
{
    echo '<wired-card class="carte-papier ' . e($classe) . '" elevation="' . $relief . '"><div class="carte-contenu">';
}

function carte_fin(): void
{
    echo '</div></wired-card>';
}

function th_tri(string $libelle, string $cle, string $tri, string $ordre, string $prefixe = ''): string
{
    $actif = ($tri === $cle);
    $sens = ($actif && $ordre === 'ASC') ? 'desc' : 'asc';
    $fleche = $actif ? ($ordre === 'ASC' ? ' ▲' : ' ▼') : '';
    $url = url_avec([$prefixe . 'tri' => $cle, $prefixe . 'ordre' => $sens, $prefixe . 'page' => null]);
    return '<th scope="col"' . ($actif ? ' aria-sort="' . ($ordre === 'ASC' ? 'ascending' : 'descending') . '"' : '') . '>'
        . '<a class="lien-tri" href="' . e($url) . '">' . e($libelle) . $fleche . '</a></th>';
}

function afficher_pagination(array $p, string $prefixe = ''): void
{
    $s = $p['total'] > 1 ? 's' : '';
    echo '<div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-2">';
    echo '<div class="text-muted" role="status"><strong>' . e($p['total']) . '</strong> résultat' . $s
        . ($p['nb_pages'] > 1 ? ' — page ' . e($p['page']) . ' sur ' . e($p['nb_pages']) : '') . '</div>';
    if ($p['nb_pages'] > 1) {
        $debut = max(1, $p['page'] - 2);
        $fin = min($p['nb_pages'], $p['page'] + 2);
        echo '<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">';
        $item = function (string $texte, int $n, bool $desactive, bool $courant = false) use ($prefixe): string {
            $classe = 'page-item' . ($desactive ? ' disabled' : '') . ($courant ? ' active' : '');
            return '<li class="' . $classe . '"><a class="page-link" href="'
                . e(url_avec([$prefixe . 'page' => $n > 1 ? $n : null])) . '">' . $texte . '</a></li>';
        };
        echo $item('«', $p['page'] - 1, $p['page'] <= 1);
        for ($n = $debut; $n <= $fin; $n++) {
            echo $item((string) $n, $n, false, $n === $p['page']);
        }
        echo $item('»', $p['page'] + 1, $p['page'] >= $p['nb_pages']);
        echo '</ul></nav>';
    }
    echo '</div>';
}

function filtre_instantane(string $id_table): string
{
    return '<input type="search" class="form-control form-control-sm w-auto" placeholder="Filtrer les lignes affichées…"'
        . ' aria-label="Filtrer les lignes affichées" data-filtre-table="#' . e($id_table) . '">';
}

function badge_regime(string $regime): string
{
    $classe = $regime === 'alternant' ? 'text-bg-info' : 'text-bg-secondary';
    return '<span class="badge ' . $classe . '">' . e(REGIMES[$regime] ?? $regime) . '</span>';
}

function badge_statut(string $statut): string
{
    $classe = ['inscrit' => 'text-bg-success', 'diplome' => 'text-bg-primary', 'sorti' => 'text-bg-dark'][$statut] ?? 'text-bg-secondary';
    return '<span class="badge ' . $classe . '">' . e(STATUTS_SCOLARITE[$statut] ?? $statut) . '</span>';
}

function boutons_actions(string $page_form, string $page_suppr, int $id): string
{
    return '<td class="text-nowrap text-end">'
        . ($page_form !== '' ? '<a class="btn btn-sm btn-outline-primary" href="' . e($page_form) . '?id=' . $id . '">Modifier</a> ' : '')
        . '<a class="btn btn-sm btn-outline-danger" href="' . e($page_suppr) . '?id=' . $id . '">Supprimer</a></td>';
}

function afficher_tableau_apprenants(array $lignes, string $tri, string $ordre, string $prefixe = '', string $id_table = 'table-apprenants'): void
{
    ?>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="<?= e($id_table) ?>">
    <thead class="table-light"><tr>
        <?= th_tri('Nom', 'nom', $tri, $ordre, $prefixe) ?>
        <?= th_tri('Prénom', 'prenom', $tri, $ordre, $prefixe) ?>
        <?= th_tri('Promotion', 'promotion', $tri, $ordre, $prefixe) ?>
        <?= th_tri('E-mail', 'email', $tri, $ordre, $prefixe) ?>
        <?= th_tri('Téléphone', 'telephone', $tri, $ordre, $prefixe) ?>
        <?= th_tri('Régime', 'regime', $tri, $ordre, $prefixe) ?>
        <?= th_tri('Entreprise', 'entreprise', $tri, $ordre, $prefixe) ?>
        <?= th_tri('Statut', 'statut', $tri, $ordre, $prefixe) ?>
        <th scope="col"><span class="visually-hidden">Actions</span></th>
    </tr></thead>
    <tbody>
    <?php if (!$lignes) : ?>
        <tr><td colspan="9" class="text-center text-muted py-4">Aucun apprenant ne correspond.</td></tr>
    <?php endif; ?>
    <?php foreach ($lignes as $a) : ?>
        <tr>
            <td><?= e($a['nom']) ?></td>
            <td><?= e($a['prenom']) ?></td>
            <td><a href="promotion.php?id=<?= (int) $a['promotion_id'] ?>"><?= e($a['promotion_code']) ?></a></td>
            <td><?= e($a['email']) ?></td>
            <td class="text-nowrap"><?= e($a['telephone']) ?></td>
            <td><?= badge_regime($a['regime']) ?></td>
            <td><?= e($a['entreprise']) ?></td>
            <td><?= badge_statut($a['statut']) ?></td>
            <?= boutons_actions('apprenant_form.php', 'apprenant_supprimer.php', (int) $a['id']) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php
}

// $masquer : colonnes a cacher ('intervenant', 'promotion')
function afficher_tableau_interventions(array $lignes, string $tri, string $ordre, string $prefixe = '', array $masquer = [], string $id_table = 'table-interventions'): void
{
    $nb_col = 5 - count($masquer);
    ?>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle" id="<?= e($id_table) ?>">
    <thead class="table-light"><tr>
        <?php if (!in_array('intervenant', $masquer, true)) { echo th_tri('Intervenant', 'intervenant', $tri, $ordre, $prefixe); } ?>
        <?= th_tri('Matière', 'matiere', $tri, $ordre, $prefixe) ?>
        <?php if (!in_array('promotion', $masquer, true)) { echo th_tri('Promotion', 'promotion', $tri, $ordre, $prefixe); } ?>
        <?= th_tri('Volume horaire', 'volume', $tri, $ordre, $prefixe) ?>
        <th scope="col"><span class="visually-hidden">Actions</span></th>
    </tr></thead>
    <tbody>
    <?php if (!$lignes) : ?>
        <tr><td colspan="<?= $nb_col ?>" class="text-center text-muted py-4">Aucune intervention.</td></tr>
    <?php endif; ?>
    <?php foreach ($lignes as $i) : ?>
        <tr>
            <?php if (!in_array('intervenant', $masquer, true)) : ?>
                <td><a href="intervenant.php?id=<?= (int) $i['intervenant_id'] ?>"><?= e($i['intervenant_nom'] . ' ' . $i['intervenant_prenom']) ?></a></td>
            <?php endif; ?>
            <td><?= e($i['matiere_libelle']) ?> <span class="text-muted">(<?= e($i['matiere_code']) ?>)</span></td>
            <?php if (!in_array('promotion', $masquer, true)) : ?>
                <td><a href="promotion.php?id=<?= (int) $i['promotion_id'] ?>"><?= e($i['promotion_code']) ?></a></td>
            <?php endif; ?>
            <td class="text-end"><?= e(heures($i['volume_horaire'])) ?></td>
            <?= boutons_actions('', 'intervention_supprimer.php', (int) $i['id']) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php
}
