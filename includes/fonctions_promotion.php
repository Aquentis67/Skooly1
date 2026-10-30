<?php
// Tout le SQL de l'ensemble "promotion".

// Liste blanche des colonnes de tri : cle publique => expression SQL.
const COLONNES_TRI_PROMOTIONS = [
    'code' => 'p.code', 'libelle' => 'p.libelle', 'debut' => 'p.date_debut',
    'fin' => 'p.date_fin', 'effectif' => 'nb_apprenants',
];

function lister_promotions(PDO $pdo, string $tri, string $ordre, int $page): array
{
    $colonne = COLONNES_TRI_PROMOTIONS[$tri] ?? 'p.code';
    $ordre = $ordre === 'DESC' ? 'DESC' : 'ASC';
    $select = "SELECT p.id, p.code, p.libelle, p.date_debut, p.date_fin, COUNT(a.id) AS nb_apprenants
               FROM promotion p LEFT JOIN apprenant a ON a.promotion_id = p.id
               GROUP BY p.id, p.code, p.libelle, p.date_debut, p.date_fin
               ORDER BY $colonne $ordre, p.code ASC";
    return requete_paginee($pdo, 'SELECT COUNT(*) FROM promotion', $select, [], $page);
}

// Pour les listes deroulantes : id => "CODE - libelle"
function promotions_pour_select(PDO $pdo): array
{
    $res = [];
    foreach ($pdo->query('SELECT id, code, libelle FROM promotion ORDER BY code') as $p) {
        $res[(int) $p['id']] = $p['code'] . ' — ' . $p['libelle'];
    }
    return $res;
}

function trouver_promotion(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT id, code, libelle, date_debut, date_fin FROM promotion WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

function code_promotion_existe(PDO $pdo, string $code, ?int $exclure_id): bool
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM promotion WHERE code = :code AND id <> :id');
    $st->execute([':code' => $code, ':id' => $exclure_id ?? 0]);
    return (int) $st->fetchColumn() > 0;
}

function creer_promotion(PDO $pdo, array $d): int
{
    $st = $pdo->prepare('INSERT INTO promotion (code, libelle, date_debut, date_fin) VALUES (:code, :libelle, :debut, :fin)');
    $st->execute([':code' => $d['code'], ':libelle' => $d['libelle'], ':debut' => $d['date_debut'], ':fin' => $d['date_fin']]);
    return (int) $pdo->lastInsertId();
}

function modifier_promotion(PDO $pdo, int $id, array $d): void
{
    $st = $pdo->prepare('UPDATE promotion SET code = :code, libelle = :libelle, date_debut = :debut, date_fin = :fin WHERE id = :id');
    $st->execute([':code' => $d['code'], ':libelle' => $d['libelle'], ':debut' => $d['date_debut'], ':fin' => $d['date_fin'], ':id' => $id]);
}

function supprimer_promotion(PDO $pdo, int $id): void
{
    $st = $pdo->prepare('DELETE FROM promotion WHERE id = :id');
    $st->execute([':id' => $id]);
}

function compter_apprenants_promotion(PDO $pdo, int $id): int
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM apprenant WHERE promotion_id = :id');
    $st->execute([':id' => $id]);
    return (int) $st->fetchColumn();
}

function compter_interventions_promotion(PDO $pdo, int $id): int
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM intervention WHERE promotion_id = :id');
    $st->execute([':id' => $id]);
    return (int) $st->fetchColumn();
}

// RG09 : message de refus (ou null si la suppression est possible).
function blocage_suppression_promotion(PDO $pdo, array $promotion): ?string
{
    $raisons = [];
    $na = compter_apprenants_promotion($pdo, (int) $promotion['id']);
    $ni = compter_interventions_promotion($pdo, (int) $promotion['id']);
    if ($na > 0) {
        $raisons[] = "elle contient $na apprenant" . ($na > 1 ? 's' : '');
    }
    if ($ni > 0) {
        $raisons[] = "elle a $ni intervention" . ($ni > 1 ? 's' : '') . ' rattachée' . ($ni > 1 ? 's' : '');
    }
    return $raisons
        ? 'Suppression refusée : la promotion ' . $promotion['code'] . ' ne peut pas être supprimée car ' . implode(' et ', $raisons) . '.'
        : null;
}
