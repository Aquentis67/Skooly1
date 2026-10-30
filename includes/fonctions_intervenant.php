<?php
// Tout le SQL de l'ensemble "intervenant".

const COLONNES_TRI_INTERVENANTS = [
    'nom' => 'i.nom', 'prenom' => 'i.prenom', 'email' => 'i.email', 'statut' => 'i.statut', 'tarif' => 'i.tarif_horaire',
];

function lister_intervenants(PDO $pdo, array $f, string $tri, string $ordre, int $page): array
{
    $w = [];
    $params = [];
    if (($f['q'] ?? '') !== '') {
        $w[] = '(i.nom LIKE :q1 OR i.prenom LIKE :q2 OR i.email LIKE :q3)';
        $params[':q1'] = $params[':q2'] = $params[':q3'] = motif_like($f['q']);
    }
    if (($f['statut'] ?? '') !== '') {
        $w[] = 'i.statut = :statut';
        $params[':statut'] = $f['statut'];
    }
    $where = $w ? 'WHERE ' . implode(' AND ', $w) : '';
    $colonne = COLONNES_TRI_INTERVENANTS[$tri] ?? 'i.nom';
    $ordre = $ordre === 'DESC' ? 'DESC' : 'ASC';

    // Sous-requete : un intervenant sans cours (RG05) apparait quand meme, avec une liste vide.
    $select = "SELECT i.id, i.nom, i.prenom, i.email, i.telephone, i.statut, i.tarif_horaire,
                 (SELECT GROUP_CONCAT(DISTINCT m.libelle ORDER BY m.libelle SEPARATOR ', ')
                    FROM intervention iv JOIN matiere m ON m.id = iv.matiere_id
                   WHERE iv.intervenant_id = i.id) AS matieres
               FROM intervenant i $where
               ORDER BY $colonne $ordre, i.nom ASC, i.prenom ASC, i.id ASC";
    return requete_paginee($pdo, "SELECT COUNT(*) FROM intervenant i $where", $select, $params, $page);
}

function intervenants_pour_select(PDO $pdo): array
{
    $res = [];
    foreach ($pdo->query('SELECT id, nom, prenom FROM intervenant ORDER BY nom, prenom') as $i) {
        $res[(int) $i['id']] = $i['nom'] . ' ' . $i['prenom'];
    }
    return $res;
}

function trouver_intervenant(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT id, nom, prenom, email, telephone, statut, tarif_horaire FROM intervenant WHERE id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

function email_intervenant_existe(PDO $pdo, string $email, ?int $exclure_id): bool
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM intervenant WHERE email = :email AND id <> :id');
    $st->execute([':email' => $email, ':id' => $exclure_id ?? 0]);
    return (int) $st->fetchColumn() > 0;
}

function preparer_intervenant(array $v): array
{
    return [
        'nom' => $v['nom'], 'prenom' => $v['prenom'], 'email' => $v['email'],
        'telephone' => $v['telephone'] === '' ? null : normaliser_telephone($v['telephone']),
        'statut' => $v['statut'],
        'tarif_horaire' => $v['statut'] === 'vacataire' ? number_format((float) str_replace(',', '.', $v['tarif_horaire']), 2, '.', '') : null,
    ];
}

function creer_intervenant(PDO $pdo, array $d): int
{
    $st = $pdo->prepare('INSERT INTO intervenant (nom, prenom, email, telephone, statut, tarif_horaire)
                         VALUES (:nom, :prenom, :email, :tel, :statut, :tarif)');
    $st->execute([':nom' => $d['nom'], ':prenom' => $d['prenom'], ':email' => $d['email'], ':tel' => $d['telephone'],
                  ':statut' => $d['statut'], ':tarif' => $d['tarif_horaire']]);
    return (int) $pdo->lastInsertId();
}

function modifier_intervenant(PDO $pdo, int $id, array $d): void
{
    $st = $pdo->prepare('UPDATE intervenant SET nom = :nom, prenom = :prenom, email = :email, telephone = :tel,
                         statut = :statut, tarif_horaire = :tarif WHERE id = :id');
    $st->execute([':nom' => $d['nom'], ':prenom' => $d['prenom'], ':email' => $d['email'], ':tel' => $d['telephone'],
                  ':statut' => $d['statut'], ':tarif' => $d['tarif_horaire'], ':id' => $id]);
}

function supprimer_intervenant(PDO $pdo, int $id): void
{
    $st = $pdo->prepare('DELETE FROM intervenant WHERE id = :id');
    $st->execute([':id' => $id]);
}

function blocage_suppression_intervenant(PDO $pdo, array $intervenant): ?string
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM intervention WHERE intervenant_id = :id');
    $st->execute([':id' => $intervenant['id']]);
    $n = (int) $st->fetchColumn();
    return $n > 0
        ? "Suppression refusée : {$intervenant['prenom']} {$intervenant['nom']} a $n intervention" . ($n > 1 ? 's' : '')
          . '. Supprimez d’abord ses interventions.'
        : null;
}
