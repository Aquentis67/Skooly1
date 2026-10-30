<?php
// SQL du tableau de bord : un compteur par ensemble de donnees.
function compter_ensembles(PDO $pdo): array
{
    return $pdo->query("SELECT
        (SELECT COUNT(*) FROM apprenant)    AS apprenants,
        (SELECT COUNT(*) FROM promotion)    AS promotions,
        (SELECT COUNT(*) FROM intervenant)  AS intervenants,
        (SELECT COUNT(*) FROM matiere)      AS matieres,
        (SELECT COUNT(*) FROM salle)        AS salles,
        (SELECT COUNT(*) FROM intervention) AS interventions")->fetch();
}
