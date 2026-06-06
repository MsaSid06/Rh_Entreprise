<?php
require_once "config/database.php";

function getCongesEnAttente(): array
{
    global $pdo;
    $stmt = $pdo->query("
        SELECT
            c.id_conge,
            e.nom_emp        AS nom,
            e.prenom_emp     AS prenom,
            c.type_conge     AS type,
            c.date_debut_conge AS date_debut,
            c.date_fin_conge   AS date_fin,
            DATEDIFF(c.date_fin_conge, c.date_debut_conge) + 1 AS nb_jours
        FROM Conge c
        JOIN Employe e ON e.matricule_emp = c.matricule_emp
        WHERE c.statut_conge = 'demande'
        ORDER BY c.date_debut_conge
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHistoriqueConges(): array
{
    global $pdo;
    $stmt = $pdo->query("
        SELECT
            c.id_conge,
            e.nom_emp          AS nom,
            e.prenom_emp       AS prenom,
            c.type_conge       AS type,
            c.date_debut_conge AS date_debut,
            c.date_fin_conge   AS date_fin,
            DATEDIFF(c.date_fin_conge, c.date_debut_conge) + 1 AS nb_jours,
            c.statut_conge     AS statut
        FROM Conge c
        JOIN Employe e ON e.matricule_emp = c.matricule_emp
        ORDER BY c.date_debut_conge DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateStatutConge(int $id_conge, string $nouveau_statut): bool|string
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE Conge
            SET statut_conge = :statut
            WHERE id_conge = :id_conge
        ");
        $stmt->execute([
            ':statut'   => $nouveau_statut,
            ':id_conge' => $id_conge,
        ]);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function traiterConge(string $nouveau_statut): bool|string
{
    if (isset($_POST['id_conge'])) {
        return updateStatutConge((int) $_POST['id_conge'], $nouveau_statut);
    }
    return false;
}

// traitement des formulaires

$message = "";

if (isset($_POST['approuver_conge'])) {
    $return  = traiterConge('approuve');
    $message = ($return === true)
        ? "Congé approuvé avec succès."
        : "Erreur : $return";
}

if (isset($_POST['refuser_conge'])) {
    $return  = traiterConge('refuse');
    $message = ($return === true)
        ? "Congé refusé avec succès."
        : "Erreur : $return";
}

$congesAttente = getCongesEnAttente();
$historique    = getHistoriqueConges();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Traitement des demandes de congé</title>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
        </ul>
    </nav>
    <?php if (!empty($message)) : ?>
        <script>alert("<?= addslashes($message) ?>");</script>
    <?php endif; ?>
</header>

<!-- Traitement des demandes qui sont en attente -->
<div class="result-container" id="traiter-conge-section">
    <h2>Demandes de congé en attente</h2>

    <?php if (empty($congesAttente)) : ?>
        <p>Aucune demande en attente.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Nombre de jours</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($congesAttente as $c) : ?>
                    <tr>
                        <td><?= $c['id_conge'] ?></td>
                        <td><?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?></td>
                        <td><?= htmlspecialchars($c['type']) ?></td>
                        <td><?= $c['date_debut'] ?></td>
                        <td><?= $c['date_fin'] ?></td>
                        <td style="text-align:center;"><?= $c['nb_jours'] ?></td>
                        <td>
                            <form method="post" action="" style="display:inline;">
                                <input type="hidden" name="id_conge" value="<?= $c['id_conge'] ?>">
                                <input type="submit" name="approuver_conge" value="Approuver">
                            </form>
                            <form method="post" action="" style="display:inline;">
                                <input type="hidden" name="id_conge" value="<?= $c['id_conge'] ?>">
                                <input type="submit" name="refuser_conge" value="Refuser">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Historique des congés -->
<div class="result-container" id="historique-section">
    <h2>Historique de tous les congés</h2>

    <?php if (empty($historique)) : ?>
        <p>Aucun congé enregistré.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Nb jours</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historique as $h) : ?>
                    <?php
                    $couleur = match($h['statut']) {
                        'approuve' => '#e8f5e9',
                        'refuse'   => '#ffebee',
                        default    => '#fff9c4',
                    };
                    ?>
                    <tr style="background-color:<?= $couleur ?>;">
                        <td><?= $h['id_conge'] ?></td>
                        <td><?= htmlspecialchars($h['nom'] . ' ' . $h['prenom']) ?></td>
                        <td><?= htmlspecialchars($h['type']) ?></td>
                        <td><?= $h['date_debut'] ?></td>
                        <td><?= $h['date_fin'] ?></td>
                        <td style="text-align:center;"><?= $h['nb_jours'] ?></td>
                        <td style="text-align:center;"><strong><?= ucfirst($h['statut']) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>