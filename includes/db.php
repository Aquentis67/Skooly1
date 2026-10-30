<?php
// Fichier de connexion : fournit l'objet PDO unique de l'application.

function config(): array
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../config/config.php';
    }
    return $config;
}

function pdo(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = config();
        $dsn = "mysql:host={$c['hote']};port={$c['port']};dbname={$c['base']};charset=utf8mb4";
        $pdo = new PDO($dsn, $c['utilisateur'], $c['mot_de_passe'], [
            // Les erreurs SQL deviennent des exceptions : on ne les rate jamais.
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Vraies requetes preparees : la requete et les donnees voyagent
            // separement jusqu'a MySQL, une valeur ne peut jamais etre
            // interpretee comme du SQL (et les types int/decimal sont respectes).
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
