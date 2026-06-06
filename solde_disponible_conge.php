<?php
require_once "config/database.php";

function getSoldesCongesAnnuels(int $annee): array
{
    global $pdo;
    // Requête qui calcule le solde de congé annuel de chaque employé
    $stmt = $pdo->prepare("
        SELECT
            e.matricule_emp,
            e.nom_emp,
            e.prenom_emp,
            30 - COALESCE(SUM(
                CASE
                    WHEN c.type_conge            = 'annuel'
                     AND c.statut_conge          = 'approuve'
                     AND YEAR(c.date_debut_conge) = :annee
                    THEN DATEDIFF(c.date_fin_conge, c.date_debut_conge) + 1
                    ELSE 0
                END
            ), 0) AS solde_restant
        FROM Employe e
        LEFT JOIN Conge c ON c.matricule_emp = e.matricule_emp
        GROUP BY e.matricule_emp, e.nom_emp, e.prenom_emp
        ORDER BY e.nom_emp, e.prenom_emp
    ");
    $stmt->execute([':annee' => $annee]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$soldes = getSoldesCongesAnnuels((int) date('Y'));

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Soldes de congés annuels</title>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
        </ul>
    </nav>
</header>

<!-- Soldes des congés annuel -->
<div class="result-container" id="soldes-section">

    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Solde restant (jours)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($soldes as $s) : ?>
                <tr>
                    <td><?= htmlspecialchars($s['matricule_emp']) ?></td>
                    <td><?= htmlspecialchars($s['nom_emp']) ?></td>
                    <td><?= htmlspecialchars($s['prenom_emp']) ?></td>
                    <td style="text-align:center; font-weight:bold;
                               color:<?= $s['solde_restant'] < 5 ? 'red' : 'green' ?>;">
                        <?= $s['solde_restant'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>