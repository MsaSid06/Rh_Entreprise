<?php
require_once "config/database.php";

function getAllEmployes(): array
{
    global $pdo;
    $stmt = $pdo->query("
        SELECT matricule_emp, nom_emp, prenom_emp
        FROM Employe
        ORDER BY nom_emp, prenom_emp
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function aContratActif(string $matricule): bool
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM Contrat
        WHERE matricule_emp = :matricule
          AND est_actif = 1
    ");
    $stmt->execute([':matricule' => $matricule]);
    return (int) $stmt->fetchColumn() > 0;
}

function getSoldeCongeAnnuel(string $matricule, int $annee): int
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(DATEDIFF(date_fin_conge, date_debut_conge) + 1), 0)
        FROM Conge
        WHERE matricule_emp         = :matricule
          AND type_conge            = 'annuel'
          AND statut_conge          = 'approuve'
          AND YEAR(date_debut_conge) = :annee
    ");
    $stmt->execute([':matricule' => $matricule, ':annee' => $annee]);
    return max(0, 30 - (int) $stmt->fetchColumn());
}

function insertConge(
    string $matricule,
    string $type,
    string $statut,
    string $date_debut,
    string $date_fin,
    int    $nb_jour_restant
): bool|string {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO Conge
                (matricule_emp, type_conge, statut_conge, date_debut_conge, date_fin_conge, nb_jour_restant)
            VALUES
                (:matricule, :type, :statut, :date_debut_conge, :date_fin_conge, :nb_jour_restant)
        ");
        $stmt->execute([
            ':matricule'        => $matricule,
            ':type'             => $type,
            ':statut'           => $statut,
            ':date_debut_conge' => $date_debut,
            ':date_fin_conge'   => $date_fin,
            ':nb_jour_restant'  => $nb_jour_restant,
        ]);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function soumettreConge(): bool|string
{
    if (isset(
        $_POST['matricule_emp_conge'],
        $_POST['type_conge'],
        $_POST['date_debut_conge'],
        $_POST['date_fin_conge']
    )) {
        $matricule  = strtoupper($_POST['matricule_emp_conge']);
        $type       = strtolower($_POST['type_conge']); 
        $statut     = 'demande';                        
        $date_debut = $_POST['date_debut_conge'];
        $date_fin   = $_POST['date_fin_conge'];

        $nb_jours = (strtotime($date_fin) - strtotime($date_debut)) / 86400 + 1; //1jour = 86400 secondes et le +1 est pour inclure le jour de début dans le calcul

        if ($nb_jours <= 0) {
            return "La date de fin doit être après la date de début.";
        }
        if (!aContratActif($matricule)) {
            return "Cet employé n'a pas de contrat actif donc congé impossible.";
        }
        if ($type === 'annuel') {
            $solde = getSoldeCongeAnnuel($matricule, (int) date('Y'));
            if ($nb_jours > $solde) {
                return "Solde insuffisant. Il reste $solde jours de congé annuel disponible.";
            }
            $nb_jour_restant = $solde - (int) $nb_jours;//on enlève les jours demandés du solde pour calculer le nb_jour_restant à stocker dans la table Conge
        } else {
            $nb_jour_restant = 0;// pour maternité et maladie on a pas de solde donc on a pour stocke 0
        }

        return insertConge($matricule, $type, $statut, $date_debut, $date_fin, $nb_jour_restant);
    }
    return false;
}

// Traitement du formulaire

$message = "";

if (isset($_POST['soumettre_conge'])) {
    $return  = soumettreConge();
    $message = ($return === true)
        ? "Demande de congé soumise avec succès."
        : "Erreur : $return";
}

$employes = getAllEmployes();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Soumettre une demande de congé</title>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
        </ul>
    </nav>
    <h4 style="color:#e9ff6a; margin-top:20px;">Tous les champs sont obligatoires</h4>
    <?php if (!empty($message)) : ?>
        <script>alert("<?= addslashes($message) ?>");</script>
    <?php endif; ?>
</header>

<!-- Soumettre une demande de congé -->
<form method="post" action="" class="form-container" id="soumettre-conge-form">
    <h2>Demande de congé</h2>

    <label for="matricule_emp_conge">Matricule de l'employé :</label>
    <select id="matricule_emp_conge" name="matricule_emp_conge" required>
        <option value="">-- Choisir un employé --</option>
        <?php foreach ($employes as $emp) : ?>
            <option value="<?= htmlspecialchars($emp['matricule_emp']) ?>">
                <?= htmlspecialchars($emp['matricule_emp'] . ' — ' . $emp['nom_emp'] . ' ' . $emp['prenom_emp']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="type_conge">Type de congé :</label>
    <select id="type_conge" name="type_conge" required>
        <option value="annuel">Annuel</option>
        <option value="maladie">Maladie</option>
        <option value="maternite">Maternité</option>
    </select><br>

    <label for="date_debut_conge">Date de début :</label>
    <input type="date" id="date_debut_conge" name="date_debut_conge" required><br>

    <label for="date_fin_conge">Date de fin :</label>
    <input type="date" id="date_fin_conge" name="date_fin_conge" required><br>

    <input type="submit" value="Soumettre la demande" name="soumettre_conge">
</form>

</body>
</html>