<?php
// Tout le SQL de l'ensemble "intervention".

const COLONNES_TRI_INTERVENTIONS = [
    'intervenant' => 'i.nom', 'matiere' => 'm.libelle', 'promotion' => 'p.code', 'volume' => 'iv.volume_horaire',
];

// $f : intervenant_id, matiere_id, promotion_id (tous optionnels)
function filtres_interventions(array $f): array
{
    $w = [];
    $params = [];
    foreach (['intervenant_id' => 'iv.intervenant_id', 'matiere_id' => 'iv.matiere_id', 'promotion_id' => 'iv.promotion_id'] as $cle => $colonne) {
        if (!empty($f[$cle])) {
            $w[] = "$colonne = :$cle";
            $params[":$cle"] = (int) $f[$cle];
        }
    }
    return [$w ? 'WHERE ' . implode(' AND ', $w) : '', $params];
}

function lister_interventions(PDO $pdo, array $f, string $tri, string $ordre, int $page, int $taille = TAILLE_PAGE): array
{
    [$where, $params] = filtres_interventions($f);
    $colonne = COLONNES_TRI_INTERVENTIONS[$tri] ?? 'i.nom';
    $ordre = $ordre === 'DESC' ? 'DESC' : 'ASC';
    $from = "FROM intervention iv
             JOIN intervenant i ON i.id = iv.intervenant_id
             JOIN matiere m ON m.id = iv.matiere_id
             JOIN promotion p ON p.id = iv.promotion_id $where";
    $select = "SELECT iv.id, iv.volume_horaire,
                      i.id AS intervenant_id, i.nom AS intervenant_nom, i.prenom AS intervenant_prenom,
                      m.id AS matiere_id, m.code AS matiere_code, m.libelle AS matiere_libelle,
                      p.id AS promotion_id, p.code AS promotion_code
               $from ORDER BY $colonne $ordre, i.nom ASC, m.libelle ASC, iv.id ASC";
    return requete_paginee($pdo, "SELECT COUNT(*) $from", $select, $params, $page, $taille);
}

function total_heures_interventions(PDO $pdo, array $f): string
{
    [$where, $params] = filtres_interventions($f);
    $st = $pdo->prepare("SELECT COALESCE(SUM(iv.volume_horaire), 0) FROM intervention iv $where");
    $st->execute($params);
    return (string) $st->fetchColumn();
}

// RG06 : volume d'une matiere dans une promotion = somme de ses interventions.
function volumes_par_matiere(PDO $pdo, int $promotion_id): array
{
    $st = $pdo->prepare('SELECT m.code, m.libelle, SUM(iv.volume_horaire) AS total
                         FROM intervention iv JOIN matiere m ON m.id = iv.matiere_id
                         WHERE iv.promotion_id = :id GROUP BY m.id, m.code, m.libelle ORDER BY m.libelle');
    $st->execute([':id' => $promotion_id]);
    return $st->fetchAll();
}

function trouver_intervention(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT iv.id, iv.volume_horaire, i.nom AS intervenant_nom, i.prenom AS intervenant_prenom,
                                m.libelle AS matiere_libelle, p.code AS promotion_code
                         FROM intervention iv
                         JOIN intervenant i ON i.id = iv.intervenant_id
                         JOIN matiere m ON m.id = iv.matiere_id
                         JOIN promotion p ON p.id = iv.promotion_id
                         WHERE iv.id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

function intervention_existe(PDO $pdo, int $intervenant_id, int $matiere_id, int $promotion_id): bool
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM intervention WHERE intervenant_id = :i AND matiere_id = :m AND promotion_id = :p');
    $st->execute([':i' => $intervenant_id, ':m' => $matiere_id, ':p' => $promotion_id]);
    return (int) $st->fetchColumn() > 0;
}

function creer_intervention(PDO $pdo, int $intervenant_id, int $matiere_id, int $promotion_id, string $volume): int
{
    $pdo->beginTransaction();
    try {
        $st = $pdo->prepare('INSERT INTO intervention (intervenant_id, matiere_id, promotion_id, volume_horaire)
                             VALUES (:i, :m, :p, :v)');
        $st->execute([':i' => $intervenant_id, ':m' => $matiere_id, ':p' => $promotion_id, ':v' => $volume]);
        $id = (int) $pdo->lastInsertId();
        // Enseigner une matiere dans une promotion suppose de pouvoir l'enseigner : on declare le lien (sans doublon).
        $st = $pdo->prepare('INSERT IGNORE INTO intervenant_matiere (intervenant_id, matiere_id) VALUES (:i, :m)');
        $st->execute([':i' => $intervenant_id, ':m' => $matiere_id]);
        $pdo->commit();
        return $id;
    } catch (Throwable $t) {
        $pdo->rollBack();
        throw $t;
    }
}

function supprimer_intervention(PDO $pdo, int $id): void
{
    $st = $pdo->prepare('DELETE FROM intervention WHERE id = :id');
    $st->execute([':id' => $id]);
}
