<?php
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/layout.php';
require __DIR__ . '/../includes/vues.php';

demarrer_session();
if (est_connecte()) {
    rediriger('index.php');
}

$erreur = '';
$identifiant = '';
if (requete_post()) {
    csrf_verifier();
    $identifiant = trim((string) ($_POST['identifiant'] ?? ''));
    $mot_de_passe = (string) ($_POST['mot_de_passe'] ?? '');
    $utilisateur = verifier_identifiants(pdo(), $identifiant, $mot_de_passe);
    if ($utilisateur !== null) {
        ouvrir_session($utilisateur);
        rediriger('index.php');
    }
    // Meme message que l'identifiant existe ou non.
    $erreur = 'Identifiant ou mot de passe incorrect.';
}

header('Cache-Control: no-store');
entete('Connexion', '', false);
?>
<div class="page-connexion">
    <div class="text-center mb-4">
        <h1>Skool<span class="marque-y">Y</span></h1>
        <p class="text-muted">Gestion du Campus ExpertY</p>
    </div>
    <?php if (isset($_GET['deconnecte'])) : ?>
        <div class="alert alert-success" role="status">Vous êtes déconnecté.</div>
    <?php endif; ?>
    <?php if ($erreur !== '') : ?>
        <div class="alert alert-danger" role="alert"><?= e($erreur) ?></div>
    <?php endif; ?>
    <?php carte_debut(); ?>
    <form method="post" action="login.php">
        <?= csrf_champ() ?>
        <div class="mb-3">
            <label for="identifiant" class="form-label">Identifiant</label>
            <input class="form-control" id="identifiant" name="identifiant" value="<?= e($identifiant) ?>"
                   autocomplete="username" maxlength="50" required autofocus>
        </div>
        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input class="form-control" id="mot_de_passe" name="mot_de_passe" type="password"
                   autocomplete="current-password" required>
        </div>
        <button class="btn btn-primary" type="submit">Se connecter</button>
    </form>
    <?php carte_fin(); ?>
</div>
<?php pied(); ?>
