<?php
// Tout le SQL de l'ensemble "salle".

const COLONNES_TRI_SALLES = [
    'numero' => 's.numero', 'libelle' => 's.libelle', 'capacite' => 's.capacite',
    'batiment' => 's.batiment', 'videoprojecteur' => 's.videoprojecteur', 'remarques' => 's.remarques',
];

function lister_salles(PDO $pdo, string $tri, string $ordre, int $page): array
{
    $colonne = COLONNES_TRI_SALLES[$tri] ?? 's.numero';
    $ordre = $ordre === 'DESC' ? 'DESC' : 'ASC';
    $select = "SELECT s.id, s.numero, s.libelle, s.capacite, s.batiment, s.videoprojecteur, s.remarques
               FROM salle s ORDER BY $colonne $ordre, s.numero ASC";
    return requete_paginee($pdo, 'SELECT COUNT(*) FROM salle', $select, [], $page);
}

function trouver_salle(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT id, numero, libelle, capacite, batiment, videoprojecteur, remarques FROM salle WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

function numero_salle_existe(PDO $pdo, string $numero, ?int $exclure_id): bool
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM salle WHERE numero = :numero AND id <> :id');
    $st->execute([':numero' => $numero, ':id' => $exclure_id ?? 0]);
    return (int) $st->fetchColumn() > 0;
}

function parametres_salle(array $d): array
{
    return [':numero' => $d['numero'], ':libelle' => $d['libelle'], ':capacite' => (int) $d['capacite'],
            ':batiment' => $d['batiment'], ':video' => !empty($d['videoprojecteur']) ? 1 : 0,
            ':remarques' => ($d['remarques'] ?? '') === '' ? null : $d['remarques']];
}

function creer_salle(PDO $pdo, array $d): int
{
    $st = $pdo->prepare('INSERT INTO salle (numero, libelle, capacite, batiment, videoprojecteur, remarques)
                         VALUES (:numero, :libelle, :capacite, :batiment, :video, :remarques)');
    $st->execute(parametres_salle($d));
    return (int) $pdo->lastInsertId();
}

function modifier_salle(PDO $pdo, int $id, array $d): void
{
    $st = $pdo->prepare('UPDATE salle SET numero = :numero, libelle = :libelle, capacite = :capacite,
                         batiment = :batiment, videoprojecteur = :video, remarques = :remarques WHERE id = :id');
    $st->execute(parametres_salle($d) + [':id' => $id]);
}

function supprimer_salle(PDO $pdo, int $id): void
{
    $st = $pdo->prepare('DELETE FROM salle WHERE id = :id');
    $st->execute([':id' => $id]);
}
