<?php
require_once "config/database.php";

function getAllEmployes(): array
{
    global $pdo;
    $stmt = $pdo->query("SELECT matricule_emp, nom_emp, prenom_emp FROM Employe ORDER BY nom_emp");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getEmployeContrat(string $matricule): array|false
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT
            e.nom_emp  AS nom,
            e.prenom_emp  AS prenom,
            e.matricule_emp  AS matricule,
            e.code_dept AS departement,
            c.salaire_brut
        FROM Employe e
        JOIN Contrat c ON c.matricule_emp = e.matricule_emp
        WHERE e.matricule_emp = :mat
          AND c.est_actif = 1
        LIMIT 1
    ");
    $stmt->execute([':mat' => $matricule]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getNbJoursTravailles(string $matricule, int $mois, int $annee): int
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM Presence
        WHERE matricule_emp = :mat
          AND MONTH(date_presence) = :mois
          AND YEAR(date_presence)  = :annee
          AND statut_presence      = 'present'
    ");
    $stmt->execute([':mat' => $matricule, ':mois' => $mois, ':annee' => $annee]);
    return (int) $stmt->fetchColumn();
}

function getNbAbsencesInjustifiees(string $matricule, int $mois, int $annee): int
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM Presence p
        WHERE p.matricule_emp    = :mat
          AND MONTH(p.date_presence) = :mois
          AND YEAR(p.date_presence)  = :annee
          AND p.statut_presence      = 'absent'
          AND NOT EXISTS (
              SELECT 1 FROM Conge c
              WHERE c.matricule_emp  = p.matricule_emp
                AND c.statut_conge   = 'approuve'
                AND p.date_presence BETWEEN c.date_debut_conge AND c.date_fin_conge
          )
    ");
    $stmt->execute([':mat' => $matricule, ':mois' => $mois, ':annee' => $annee]);
    return (int) $stmt->fetchColumn();
}

function insertOuMajBulletin(
    string $matricule, int $mois, int $annee,
    float $salaire_brut, int $nb_jours, int $nb_absences
): bool|string {
    global $pdo;
    try {
        // On vérifie si le bulletin existe déjà pour cet employé et cette période
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM Bulletin
            WHERE matricule_emp = :mat AND mois = :mois AND annee = :annee
        ");
        $stmt->execute([':mat' => $matricule, ':mois' => $mois, ':annee' => $annee]);

        if ($stmt->fetchColumn() > 0) {
            $stmt2 = $pdo->prepare("
                UPDATE Bulletin
                SET salaire_brut        = :brut,
                    nbr_jours_travaille = :jours,
                    nbr_abs_injustifie  = :absences
                WHERE matricule_emp = :mat AND mois = :mois AND annee = :annee
            ");
        } else {
            $stmt2 = $pdo->prepare("
                INSERT INTO Bulletin
                    (matricule_emp, mois, annee, salaire_brut, nbr_jours_travaille, nbr_abs_injustifie)
                VALUES
                    (:mat, :mois, :annee, :brut, :jours, :absences)
            ");
        }

        $stmt2->execute([
            ':mat'      => $matricule,
            ':mois'     => $mois,
            ':annee'    => $annee,
            ':brut'     => $salaire_brut,
            ':jours'    => $nb_jours,
            ':absences' => $nb_absences,
        ]);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function getAllBulletins(): array
{
    global $pdo;
    $stmt = $pdo->query("
        SELECT b.*, e.nom_emp AS nom, e.prenom_emp AS prenom
        FROM Bulletin b
        JOIN Employe e ON e.matricule_emp = b.matricule_emp
        ORDER BY b.annee DESC, b.mois DESC, e.nom_emp
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$message = "";
$bulletin = null;

$mois_noms = [
    1=>'Janvier', 2=>'Février', 3=>'Mars',     4=>'Avril',
    5=>'Mai',     6=>'Juin',    7=>'Juillet',   8=>'Août',
    9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre',
];

if (isset($_POST['generer_bulletin'])) {

    if (isset($_POST['matricule_emp_bulletin'], $_POST['mois_bulletin'], $_POST['annee_bulletin'])) {

        $matricule = strtoupper($_POST['matricule_emp_bulletin']);
        $mois      = (int) $_POST['mois_bulletin'];
        $annee     = (int) $_POST['annee_bulletin'];

       //c'est pour vérifier si le contrat de l'employé est actif et pour récupérer son salaire brut
        $employe = getEmployeContrat($matricule);

        if (!$employe) {
            $message = "Erreur : Aucun contrat actif trouvé pour cet employé.";
        } else {
            $salaire_brut = (float) $employe['salaire_brut'];

            //  voirs jours travaillés et absences injustifiées
            $nb_jours    = getNbJoursTravailles($matricule, $mois, $annee);
            $nb_absences = getNbAbsencesInjustifiees($matricule, $mois, $annee);

            // calcul des retenues et salaire net
            $retenue_unitaire = round($salaire_brut / 22, 2);
            $retenues_total   = round($retenue_unitaire * $nb_absences, 2);
            $salaire_net      = round($salaire_brut - $retenues_total, 2);

            // Enregistrement en base
            $return = insertOuMajBulletin($matricule, $mois, $annee, $salaire_brut, $nb_jours, $nb_absences);

            if ($return === true) {
                $message = "Bulletin généré avec succès.";
                $bulletin = [
                    'nom'             => $employe['nom'],
                    'prenom'          => $employe['prenom'],
                    'matricule'       => $employe['matricule'],
                    'departement'     => $employe['departement'],
                    'mois'            => $mois,
                    'annee'           => $annee,
                    'salaire_brut'    => $salaire_brut,
                    'nb_jours'        => $nb_jours,
                    'nb_absences'     => $nb_absences,
                    'retenue_unit'    => $retenue_unitaire,
                    'retenues_total'  => $retenues_total,
                    'salaire_net'     => $salaire_net,
                ];
            } else {
                $message = "Erreur : $return";
            }
        }
    }
}

$employes  = getAllEmployes();
$bulletins = getAllBulletins();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Générer un Bulletin de Paie</title>
    <style>
    #bulletin-affiche {
        max-width: 480px;
        margin-left: auto !important;
        margin-right: auto !important;
        text-align: center !important;
    }
    #bulletin-affiche .bulletin-ligne {
        display: block !important;
        text-align: center !important;
    }
    #bulletin-affiche .bulletin-ligne span {
        display: inline !important;
    }
    #bulletin-affiche .bulletin-total {
        display: block !important;
        text-align: center !important;
    }
</style>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="#generer-bulletin-form">Générer un bulletin</a></li>
            <li><a href="#historique-bulletins">Historique des bulletins</a></li>
        </ul>
    </nav>
    <h4 style="color: #e9ff6a; margin-top: 20px;">Tous les champs sont obligatoires</h4>
    <?php if (!empty($message)) : ?>
        <script>alert("<?= addslashes($message) ?>");</script>
    <?php endif; ?>
</header>

<!-- formulaire de génération de bulletin -->
<form method="post" action="" class="form-container" id="generer-bulletin-form">
    <h2>Générer un Bulletin de Paie</h2>

    <label for="matricule_emp_bulletin">Employé :</label>
    <select id="matricule_emp_bulletin" name="matricule_emp_bulletin" required>
        <option value="">-- Choisir un employé --</option>
        <?php foreach ($employes as $emp) : ?>
            <option value="<?= htmlspecialchars($emp['matricule_emp']) ?>"
                <?= (isset($_POST['matricule_emp_bulletin']) && $_POST['matricule_emp_bulletin'] === $emp['matricule_emp']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($emp['matricule_emp'] . ' — ' . $emp['nom_emp'] . ' ' . $emp['prenom_emp']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="mois_bulletin">Mois :</label>
    <select id="mois_bulletin" name="mois_bulletin" required>
        <?php foreach ($mois_noms as $num => $nom) : ?>
            <option value="<?= $num ?>"
                <?= (isset($_POST['mois_bulletin']) && (int)$_POST['mois_bulletin'] === $num) ? 'selected' : '' ?>>
                <?= $nom ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="annee_bulletin">Année :</label>
    <input type="number" id="annee_bulletin" name="annee_bulletin"
           value="<?= isset($_POST['annee_bulletin']) ? (int)$_POST['annee_bulletin'] : date('Y') ?>"
           min="2020" max="2100" required><br>

    <input type="submit" value="Générer le bulletin" name="generer_bulletin">
</form>

<!-- voici lebulletin généré -->
<?php if ($bulletin) : ?>
<div class="bulletin-paie" id="bulletin-affiche">
    <h3>Bulletin de Paie</h3>

    <div class="bulletin-ligne">
        <span>Employé :</span>
        <span><?= htmlspecialchars($bulletin['nom'] . ' ' . $bulletin['prenom']) ?></span>
    </div>
    <div class="bulletin-ligne">
        <span>Matricule :</span>
        <span><?= htmlspecialchars($bulletin['matricule']) ?></span>
    </div>
    <div class="bulletin-ligne">
        <span>Département :</span>
        <span><?= htmlspecialchars($bulletin['departement']) ?></span>
    </div>
    <div class="bulletin-ligne">
        <span>Période :</span>
        <span><?= $mois_noms[$bulletin['mois']] . ' ' . $bulletin['annee'] ?></span>
    </div>

    <br>

    <div class="bulletin-ligne">
        <span>Salaire brut :</span>
        <span><?= number_format($bulletin['salaire_brut'], 2, ',', ' ') ?> EURO</span>
    </div>
    <div class="bulletin-ligne">
        <span>Jours travaillés :</span>
        <span><?= $bulletin['nb_jours'] ?> jour(s)</span>
    </div>
    <div class="bulletin-ligne">
        <span>Absences injustifiées :</span>
        <span><?= $bulletin['nb_absences'] ?> jour(s)</span>
    </div>
    <div class="bulletin-ligne" style="color:red;">
        <span>Retenue par jour d'absence non justifiée :</span>
        <span><?= number_format($bulletin['retenue_unit'], 2, ',', ' ') ?> EURO / jour</span>
    </div>
    <div class="bulletin-ligne" style="color:red;">
        <span>Total retenues :</span>
        <span>- <?= number_format($bulletin['retenues_total'], 2, ',', ' ') ?> EURO</span>
    </div>

    <div class="bulletin-total">
        <span>SALAIRE NET À PAYER :</span>
        <span><?= number_format($bulletin['salaire_net'], 2, ',', ' ') ?> EURO</span>
    </div>
</div>
<?php endif; ?>

<!-- C'est pour voir l'historique des bulletins générés -->
<div class="result-container" id="historique-bulletins">
    <h2>Historique des bulletins</h2>

    <?php if (empty($bulletins)) : ?>
        <p>Aucun bulletin généré pour l'instant.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Matricule</th>
                    <th>Période</th>
                    <th>Salaire brut</th>
                    <th>Jours travaillés</th>
                    <th>Absences injust.</th>
                    <th>Retenues (calculées)</th>
                    <th>Salaire net (calculé)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bulletins as $b) :
                    // Salaire net et retenues sont des données calculées, non stockées en base
                    $retenues    = round(($b['salaire_brut'] / 22) * $b['nbr_abs_injustifie'], 2);
                    $salaire_net = round($b['salaire_brut'] - $retenues, 2);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($b['nom'] . ' ' . $b['prenom']) ?></td>
                        <td><?= htmlspecialchars($b['matricule_emp']) ?></td>
                        <td><?= $mois_noms[$b['mois']] . ' ' . $b['annee'] ?></td>
                        <td><?= number_format($b['salaire_brut'], 2, ',', ' ') ?> EURO</td>
                        <td style="text-align:center;"><?= $b['nbr_jours_travaille'] ?></td>
                        <td style="text-align:center;"><?= $b['nbr_abs_injustifie'] ?></td>
                        <td style="color:red;">- <?= number_format($retenues, 2, ',', ' ') ?> EURO</td>
                        <td style="color:green; font-weight:bold;">
                            <?= number_format($salaire_net, 2, ',', ' ') ?> EURO
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>