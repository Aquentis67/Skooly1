<?php
// Tout le SQL de l'ensemble "apprenant".

const COLONNES_TRI_APPRENANTS = [
    'nom' => 'a.nom', 'prenom' => 'a.prenom', 'promotion' => 'p.code', 'email' => 'a.email',
    'telephone' => 'a.telephone', 'regime' => 'a.regime', 'entreprise' => 'a.entreprise', 'statut' => 'a.statut',
];

// $f : q (recherche), promotion_id, regime, statut
function lister_apprenants(PDO $pdo, array $f, string $tri, string $ordre, int $page, int $taille = TAILLE_PAGE): array
{
    $w = [];
    $params = [];
    if (($f['q'] ?? '') !== '') {
        // Un parametre nomme ne peut pas etre reutilise quand l'emulation est coupee : q1, q2, q3.
        $w[] = '(a.nom LIKE :q1 OR a.prenom LIKE :q2 OR a.email LIKE :q3)';
        $params[':q1'] = $params[':q2'] = $params[':q3'] = motif_like($f['q']);
    }
    if (!empty($f['promotion_id'])) {
        $w[] = 'a.promotion_id = :promotion_id';
        $params[':promotion_id'] = (int) $f['promotion_id'];
    }
    if (($f['regime'] ?? '') !== '') {
        $w[] = 'a.regime = :regime';
        $params[':regime'] = $f['regime'];
    }
    if (($f['statut'] ?? '') !== '') {
        $w[] = 'a.statut = :statut';
        $params[':statut'] = $f['statut'];
    }
    $where = $w ? 'WHERE ' . implode(' AND ', $w) : '';
    $colonne = COLONNES_TRI_APPRENANTS[$tri] ?? 'a.nom';   // liste blanche
    $ordre = $ordre === 'DESC' ? 'DESC' : 'ASC';

    $from = "FROM apprenant a JOIN promotion p ON p.id = a.promotion_id $where";
    $select = "SELECT a.id, a.nom, a.prenom, a.email, a.telephone, a.regime, a.entreprise, a.statut,
                      p.id AS promotion_id, p.code AS promotion_code
               $from ORDER BY $colonne $ordre, a.nom ASC, a.prenom ASC, a.id ASC";
    return requete_paginee($pdo, "SELECT COUNT(*) $from", $select, $params, $page, $taille);
}

function trouver_apprenant(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT a.*, p.code AS promotion_code FROM apprenant a JOIN promotion p ON p.id = a.promotion_id WHERE a.id = :id');
    $st->execute([':id' => $id]);
    return $st->fetch() ?: null;
}

// Convertit les valeurs du formulaire (deja validees) en valeurs pretes pour la base.
function preparer_apprenant(array $v): array
{
    return [
        'nom' => $v['nom'], 'prenom' => $v['prenom'],
        'date_naissance' => date_vers_sql($v['date_naissance']),
        'email' => $v['email'],
        'telephone' => $v['telephone'] === '' ? null : normaliser_telephone($v['telephone']),
        'regime' => $v['regime'],
        'entreprise' => ($v['regime'] === 'alternant') ? $v['entreprise'] : null,
        'statut' => $v['statut'],
        'promotion_id' => (int) $v['promotion_id'],
    ];
}

function parametres_apprenant(array $d): array
{
    return [':nom' => $d['nom'], ':prenom' => $d['prenom'], ':naiss' => $d['date_naissance'], ':email' => $d['email'],
            ':tel' => $d['telephone'], ':regime' => $d['regime'], ':ent' => $d['entreprise'],
            ':statut' => $d['statut'], ':promo' => $d['promotion_id']];
}

function creer_apprenant(PDO $pdo, array $d): int
{
    $st = $pdo->prepare('INSERT INTO apprenant (nom, prenom, date_naissance, email, telephone, regime, entreprise, statut, promotion_id)
                         VALUES (:nom, :prenom, :naiss, :email, :tel, :regime, :ent, :statut, :promo)');
    $st->execute(parametres_apprenant($d));
    return (int) $pdo->lastInsertId();
}

function modifier_apprenant(PDO $pdo, int $id, array $d): void
{
    $st = $pdo->prepare('UPDATE apprenant SET nom = :nom, prenom = :prenom, date_naissance = :naiss, email = :email,
                         telephone = :tel, regime = :regime, entreprise = :ent, statut = :statut, promotion_id = :promo
                         WHERE id = :id');
    $st->execute(parametres_apprenant($d) + [':id' => $id]);
}

function supprimer_apprenant(PDO $pdo, int $id): void
{
    $st = $pdo->prepare('DELETE FROM apprenant WHERE id = :id');
    $st->execute([':id' => $id]);
}
