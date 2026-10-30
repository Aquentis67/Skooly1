<?php
// Generateurs de champs de formulaire : tous les champs obligatoires sont
// signales de la meme facon (*) et les erreurs s'affichent a cote du champ.

function etiquette(string $id, string $libelle, bool $obligatoire): string
{
    $marque = $obligatoire
        ? ' <span class="champ-obligatoire" aria-hidden="true" title="Champ obligatoire">*</span><span class="visually-hidden">(obligatoire)</span>'
        : '';
    return '<label for="' . e($id) . '" class="form-label">' . e($libelle) . $marque . '</label>';
}

function retour_erreur(array $erreurs, string $nom): string
{
    return '<div class="invalid-feedback">' . e($erreurs[$nom] ?? '') . '</div>';
}

// $o : obligatoire, type, maxlength, placeholder, aide, regle (email|date|telephone)
function champ_texte(string $nom, string $libelle, array $valeurs, array $erreurs, array $o = []): string
{
    $o += ['obligatoire' => true, 'type' => 'text', 'maxlength' => null, 'placeholder' => '', 'aide' => '', 'regle' => ''];
    $id = 'champ-' . $nom;
    $classe = isset($erreurs[$nom]) ? ' is-invalid' : '';
    $h  = '<div class="mb-3" id="ligne-' . e($nom) . '">' . etiquette($id, $libelle, $o['obligatoire']);
    $h .= '<input class="form-control' . $classe . '" id="' . e($id) . '" name="' . e($nom) . '" type="' . e($o['type']) . '"';
    $h .= ' value="' . e($valeurs[$nom] ?? '') . '"';
    if ($o['maxlength']) {
        $h .= ' maxlength="' . (int) $o['maxlength'] . '"';
    }
    if ($o['placeholder'] !== '') {
        $h .= ' placeholder="' . e($o['placeholder']) . '"';
    }
    if ($o['regle'] !== '') {
        $h .= ' data-regle="' . e($o['regle']) . '"';
    }
    if ($o['obligatoire']) {
        $h .= ' data-obligatoire="1"';
    }
    $h .= '>' . retour_erreur($erreurs, $nom);
    if ($o['aide'] !== '') {
        $h .= '<div class="form-text">' . e($o['aide']) . '</div>';
    }
    return $h . '</div>';
}

// Zone de texte multiligne. $o : obligatoire, maxlength, rows, aide
function champ_zone(string $nom, string $libelle, array $valeurs, array $erreurs, array $o = []): string
{
    $o += ['obligatoire' => false, 'maxlength' => null, 'rows' => 3, 'aide' => ''];
    $id = 'champ-' . $nom;
    $classe = isset($erreurs[$nom]) ? ' is-invalid' : '';
    $h  = '<div class="mb-3" id="ligne-' . e($nom) . '">' . etiquette($id, $libelle, $o['obligatoire']);
    $h .= '<textarea class="form-control' . $classe . '" id="' . e($id) . '" name="' . e($nom) . '" rows="' . (int) $o['rows'] . '"';
    if ($o['maxlength']) {
        $h .= ' maxlength="' . (int) $o['maxlength'] . '"';
    }
    $h .= '>' . e($valeurs[$nom] ?? '') . '</textarea>' . retour_erreur($erreurs, $nom);
    if ($o['aide'] !== '') {
        $h .= '<div class="form-text">' . e($o['aide']) . '</div>';
    }
    return $h . '</div>';
}

// Cases a cocher multiples (name="nom[]"). $options : valeur => libelle ; $choisis : valeurs cochees
function champ_cases(string $nom, string $libelle, array $options, array $choisis, array $erreurs, array $o = []): string
{
    $o += ['aide' => ''];
    $choisis = array_map('strval', $choisis);
    $h = '<fieldset class="mb-3" id="ligne-' . e($nom) . '"><legend class="form-label fs-6">' . e($libelle) . '</legend>';
    $h .= '<div class="row row-cols-1 row-cols-md-2 g-1">';
    foreach ($options as $valeur => $texte) {
        $id = 'champ-' . $nom . '-' . (int) $valeur;
        $h .= '<div class="col"><div class="form-check"><input class="form-check-input' . (isset($erreurs[$nom]) ? ' is-invalid' : '') . '" type="checkbox" id="' . e($id) . '" name="' . e($nom) . '[]" value="' . e($valeur) . '"'
            . (in_array((string) $valeur, $choisis, true) ? ' checked' : '') . '><label class="form-check-label" for="' . e($id) . '">' . e($texte) . '</label></div></div>';
    }
    $h .= '</div>' . (isset($erreurs[$nom]) ? '<div class="text-danger small mt-1">' . e($erreurs[$nom]) . '</div>' : '');
    if ($o['aide'] !== '') {
        $h .= '<div class="form-text">' . e($o['aide']) . '</div>';
    }
    return $h . '</fieldset>';
}

// $options : valeur => libelle
function champ_select(string $nom, string $libelle, array $options, array $valeurs, array $erreurs, array $o = []): string
{
    $o += ['obligatoire' => true, 'vide' => '— Choisir —'];
    $id = 'champ-' . $nom;
    $courant = (string) ($valeurs[$nom] ?? '');
    $classe = isset($erreurs[$nom]) ? ' is-invalid' : '';
    $h  = '<div class="mb-3" id="ligne-' . e($nom) . '">' . etiquette($id, $libelle, $o['obligatoire']);
    $h .= '<select class="form-select' . $classe . '" id="' . e($id) . '" name="' . e($nom) . '"' . ($o['obligatoire'] ? ' data-obligatoire="1"' : '') . '>';
    $h .= '<option value="">' . e($o['vide']) . '</option>';
    foreach ($options as $valeur => $texte) {
        $sel = ((string) $valeur === $courant) ? ' selected' : '';
        $h .= '<option value="' . e($valeur) . '"' . $sel . '>' . e($texte) . '</option>';
    }
    return $h . '</select>' . retour_erreur($erreurs, $nom) . '</div>';
}

function champ_case(string $nom, string $libelle, array $valeurs, array $erreurs): string
{
    $id = 'champ-' . $nom;
    $coche = !empty($valeurs[$nom]) ? ' checked' : '';
    return '<div class="mb-3" id="ligne-' . e($nom) . '"><div class="form-check form-switch">'
        . '<input class="form-check-input" type="checkbox" role="switch" id="' . e($id) . '" name="' . e($nom) . '" value="1"' . $coche . '>'
        . '<label class="form-check-label" for="' . e($id) . '">' . e($libelle) . '</label></div></div>';
}

function bloc_erreurs(array $erreurs): string
{
    if (!$erreurs) {
        return '';
    }
    $n = count($erreurs);
    return '<div class="alert alert-danger" role="alert">Le formulaire contient ' . $n
        . ' erreur' . ($n > 1 ? 's' : '') . ' : rien n’a été enregistré. Corrigez les champs signalés en rouge.</div>';
}

function note_champs_obligatoires(): string
{
    return '<p class="text-muted small">Les champs marqués d’un <span class="champ-obligatoire">*</span> sont obligatoires.</p>';
}
