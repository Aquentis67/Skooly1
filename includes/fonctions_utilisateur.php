<?php
// Tout le SQL de l'ensemble "utilisateur".

function trouver_utilisateur(PDO $pdo, string $identifiant): ?array
{
    $st = $pdo->prepare('SELECT id, identifiant, mot_de_passe_hash FROM utilisateur WHERE identifiant = :identifiant');
    $st->execute([':identifiant' => $identifiant]);
    return $st->fetch() ?: null;
}

function maj_hash_utilisateur(PDO $pdo, int $id, string $hash): void
{
    $st = $pdo->prepare('UPDATE utilisateur SET mot_de_passe_hash = :hash WHERE id = :id');
    $st->execute([':hash' => $hash, ':id' => $id]);
}
