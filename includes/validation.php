<?php
// Fonctions de validation cote serveur. Chaque validateur renvoie un tableau
// champ => message d'erreur (vide si tout est valide).

function valider_texte(array &$err, string $champ, string $valeur, string $libelle, int $max, bool $obligatoire = true): void
{
    if ($valeur === '') {
        if ($obligatoire) {
            $err[$champ] = "$libelle est obligatoire.";
        }
    } elseif (!preg_match('//u', $valeur)) {
        $err[$champ] = "$libelle contient des caractères invalides.";
    } elseif (longueur($valeur) > $max) {
        $err[$champ] = "$libelle est trop long ($max caractères maximum).";
    }
}

function valider_email(array &$err, string $champ, string $valeur): void
{
    valider_texte($err, $champ, $valeur, "L'adresse électronique", 150);
    if (isset($err[$champ]) || $valeur === '') {
        return;
    }
    $domaine = substr(strrchr($valeur, '@') ?: '', 1);
    if (!filter_var($valeur, FILTER_VALIDATE_EMAIL) || !str_contains($domaine, '.')) {
        $err[$champ] = "L'adresse électronique n'est pas valide (exemple : nom@domaine.fr).";
    }
}

function valider_telephone(array &$err, string $champ, string $valeur): void
{
    if ($valeur !== '' && normaliser_telephone($valeur) === null) {
        $err[$champ] = 'Le téléphone n’est pas valide (exemple : 06 12 34 56 78).';
    }
}

// Valide une date JJ/MM/AAAA ; $min et $max (format SQL) sont optionnels.
function valider_date(array &$err, string $champ, string $valeur, string $libelle, ?string $min = null, ?string $max = null): ?string
{
    if ($valeur === '') {
        $err[$champ] = "$libelle est obligatoire.";
        return null;
    }
    $sql = date_vers_sql($valeur);
    if ($sql === null) {
        $err[$champ] = "$libelle n'est pas une date valide (format JJ/MM/AAAA, la date doit exister).";
        return null;
    }
    if ($min !== null && $sql < $min) {
        $err[$champ] = "$libelle est trop ancienne.";
    } elseif ($max !== null && $sql > $max) {
        $err[$champ] = "$libelle est trop récente ou dans le futur.";
    }
    return $sql;
}

function valider_dans_liste(array &$err, string $champ, string $valeur, array $autorises, string $libelle): void
{
    if ($valeur === '') {
        $err[$champ] = "$libelle est obligatoire.";
    } elseif (!array_key_exists($valeur, $autorises)) {
        $err[$champ] = "$libelle : valeur non autorisée.";
    }
}

// Entier strictement positif (ex. capacite).
function valider_entier_positif(array &$err, string $champ, string $valeur, string $libelle, int $max): void
{
    if ($valeur === '') {
        $err[$champ] = "$libelle est obligatoire.";
    } elseif (!preg_match('/^\d+$/', $valeur) || (int) $valeur < 1 || (int) $valeur > $max) {
        $err[$champ] = "$libelle doit être un entier compris entre 1 et $max.";
    }
}

// Decimal positif a 2 decimales maximum ; accepte la virgule. Retourne "12.50" ou null.
function valider_decimal_positif(array &$err, string $champ, string $valeur, string $libelle, float $max): ?string
{
    $v = str_replace(',', '.', $valeur);
    if ($valeur === '') {
        $err[$champ] = "$libelle est obligatoire.";
        return null;
    }
    if (!preg_match('/^\d{1,4}(\.\d{1,2})?$/', $v) || (float) $v <= 0 || (float) $v > $max) {
        $err[$champ] = "$libelle doit être un nombre positif, 2 décimales maximum, ne dépassant pas " . $max . '.';
        return null;
    }
    return number_format((float) $v, 2, '.', '');
}

// Cle etrangere : l'identifiant doit exister dans la liste fournie (id => libelle).
function valider_reference(array &$err, string $champ, string $valeur, array $existants, string $libelle): void
{
    if ($valeur === '') {
        $err[$champ] = "$libelle est obligatoire.";
    } elseif (!ctype_digit($valeur) || !array_key_exists((int) $valeur, $existants)) {
        $err[$champ] = "$libelle : choix inconnu.";
    }
}

// ------------------------------------------------------ validateurs d'entite
function valider_apprenant(array $v, array $promotions): array
{
    $err = [];
    valider_texte($err, 'nom', $v['nom'], 'Le nom', 60);
    valider_texte($err, 'prenom', $v['prenom'], 'Le prénom', 60);
    valider_date($err, 'date_naissance', $v['date_naissance'], 'La date de naissance',
        '1940-01-01', date('Y-m-d', strtotime('-14 years')));
    valider_email($err, 'email', $v['email']);
    valider_telephone($err, 'telephone', $v['telephone']);
    valider_dans_liste($err, 'regime', $v['regime'], REGIMES, 'Le régime');
    valider_dans_liste($err, 'statut', $v['statut'], STATUTS_SCOLARITE, 'Le statut de scolarité');
    valider_reference($err, 'promotion_id', $v['promotion_id'], $promotions, 'La promotion');
    if ($v['regime'] === 'alternant' && $v['entreprise'] === '') {
        $err['entreprise'] = "L'entreprise d'accueil est obligatoire pour un alternant.";
    } else {
        valider_texte($err, 'entreprise', $v['entreprise'], "L'entreprise d'accueil", 100, false);
    }
    return $err;
}

function valider_promotion(PDO $pdo, array $v, ?int $id): array
{
    $err = [];
    valider_texte($err, 'code', $v['code'], 'Le code', 10);
    if (!isset($err['code'])) {
        if (!preg_match('/^[A-Za-z0-9 ._-]+$/', $v['code'])) {
            $err['code'] = 'Le code ne peut contenir que lettres, chiffres, espace, point, tiret.';
        } elseif (code_promotion_existe($pdo, $v['code'], $id)) {
            $err['code'] = 'Ce code est déjà utilisé par une autre promotion.';
        }
    }
    valider_texte($err, 'libelle', $v['libelle'], 'Le libellé', 100);
    $debut = valider_date($err, 'date_debut', $v['date_debut'], 'La date de début', '2000-01-01', '2100-12-31');
    $fin = valider_date($err, 'date_fin', $v['date_fin'], 'La date de fin', '2000-01-01', '2100-12-31');
    if ($debut && $fin && $fin <= $debut) {
        $err['date_fin'] = 'La date de fin doit être postérieure à la date de début.';
    }
    return $err;
}

function valider_intervenant(PDO $pdo, array $v, ?int $id): array
{
    $err = [];
    valider_texte($err, 'nom', $v['nom'], 'Le nom', 60);
    valider_texte($err, 'prenom', $v['prenom'], 'Le prénom', 60);
    valider_email($err, 'email', $v['email']);
    if (!isset($err['email']) && email_intervenant_existe($pdo, $v['email'], $id)) {
        $err['email'] = 'Cette adresse électronique est déjà utilisée par un autre intervenant.';
    }
    valider_telephone($err, 'telephone', $v['telephone']);
    valider_dans_liste($err, 'statut', $v['statut'], STATUTS_INTERVENANT, 'Le statut');
    if ($v['statut'] === 'vacataire') {
        valider_decimal_positif($err, 'tarif_horaire', $v['tarif_horaire'], 'Le tarif horaire', 999.99);
    } elseif ($v['statut'] === 'salarie' && $v['tarif_horaire'] !== '') {
        $err['tarif_horaire'] = 'Le tarif horaire est réservé aux vacataires : laissez ce champ vide pour un salarié.';
    }
    return $err;
}

function valider_matiere(PDO $pdo, array $v, ?int $id): array
{
    $err = [];
    valider_texte($err, 'code', $v['code'], 'Le code', 10);
    if (!isset($err['code'])) {
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $v['code'])) {
            $err['code'] = 'Le code ne peut contenir que lettres, chiffres, point, tiret (pas d’espace).';
        } elseif (code_matiere_existe($pdo, $v['code'], $id)) {
            $err['code'] = 'Ce code est déjà utilisé par une autre matière.';
        }
    }
    valider_texte($err, 'libelle', $v['libelle'], 'Le libellé', 100);
    return $err;
}

function valider_salle(PDO $pdo, array $v, ?int $id): array
{
    $err = [];
    valider_texte($err, 'numero', $v['numero'], 'Le numéro', 10);
    if (!isset($err['numero']) && numero_salle_existe($pdo, $v['numero'], $id)) {
        $err['numero'] = 'Ce numéro est déjà utilisé par une autre salle.';
    }
    valider_texte($err, 'libelle', $v['libelle'], 'Le libellé', 60);
    valider_entier_positif($err, 'capacite', $v['capacite'], 'La capacité', 1000);
    valider_texte($err, 'batiment', $v['batiment'], 'Le bâtiment', 40);
    valider_texte($err, 'remarques', $v['remarques'] ?? '', 'Les remarques', 500, false);
    return $err;
}

function valider_intervention(PDO $pdo, array $v, array $intervenants, array $matieres, array $promotions): array
{
    $err = [];
    valider_reference($err, 'intervenant_id', $v['intervenant_id'], $intervenants, "L'intervenant");
    valider_reference($err, 'matiere_id', $v['matiere_id'], $matieres, 'La matière');
    valider_reference($err, 'promotion_id', $v['promotion_id'], $promotions, 'La promotion');
    valider_decimal_positif($err, 'volume_horaire', $v['volume_horaire'], 'Le volume horaire', 999.99);
    if (!$err && intervention_existe($pdo, (int) $v['intervenant_id'], (int) $v['matiere_id'], (int) $v['promotion_id'])) {
        $err['matiere_id'] = 'Cette intervention existe déjà (même intervenant, même matière, même promotion).';
    }
    return $err;
}
