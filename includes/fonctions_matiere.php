<?php
// Tout le SQL de l'ensemble "matiere".

const COLONNES_TRI_MATIERES = ['code' => 'm.code', 'libelle' => 'm.libelle', 'interventions' => 'nb_interventions'];

function lister_matieres(PDO $pdo, string $tri, string $ordre, int $page): array
{
    $colonne = COLONNES_TRI_MATIERES[$tri] ?? 'm.code';
    $ordre = $ordre === 'DESC' ? 'DESC' : 'ASC';
    $select = "SELECT m.id, m.code, m.libelle, COUNT(iv.id) AS nb_interventions
               FROM matiere m LEFT JOIN intervention iv ON iv.matiere_id = m.id
               GROUP BY m.id, m.code, m.libelle
               ORDER BY $colonne $ordre, m.code ASC";
    return requete_paginee($pdo, 'SELECT COUNT(*) FROM matiere', $select, [], $page);
}

function matieres_pour_select(PDO $pdo): array
{
    $res = [];
    foreach ($pdo->query('SELECT id, code, libelle FROM matiere ORDER BY libelle') as $m) {
        $res[(int) $m['id']] = $m['libelle'] . ' (' . $m['code'] . ')';
    }
    return $res;
}

function trouver_matiere(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT id, code, libelle FROM matiere WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

function code_matiere_existe(PDO $pdo, string $code, ?int $exclure_id): bool
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM matiere WHERE code = :code AND id <> :id');
    $st->execute([':code' => $code, ':id' => $exclure_id ?? 0]);
    return (int) $st->fetchColumn() > 0;
}

function creer_matiere(PDO $pdo, array $d): int
{
    $st = $pdo->prepare('INSERT INTO matiere (code, libelle) VALUES (:code, :libelle)');
    $st->execute([':code' => $d['code'], ':libelle' => $d['libelle']]);
    return (int) $pdo->lastInsertId();
}

function modifier_matiere(PDO $pdo, int $id, array $d): void
{
    $st = $pdo->prepare('UPDATE matiere SET code = :code, libelle = :libelle WHERE id = :id');
    $st->execute([':code' => $d['code'], ':libelle' => $d['libelle'], ':id' => $id]);
}

function supprimer_matiere(PDO $pdo, int $id): void
{
    $st = $pdo->prepare('DELETE FROM matiere WHERE id = :id');
    $st->execute([':id' => $id]);
}

function blocage_suppression_matiere(PDO $pdo, array $matiere): ?string
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM intervention WHERE matiere_id = :id');
    $st->execute([':id' => $matiere['id']]);
    $n = (int) $st->fetchColumn();
    return $n > 0
        ? "Suppression refusée : la matière {$matiere['libelle']} est utilisée par $n intervention" . ($n > 1 ? 's' : '') . '.'
        : null;
}
