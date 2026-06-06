<?php
require_once "config/database.php";

function getAllEmployes(): array
{
    global $pdo;
    $stmt = $pdo->query("SELECT matricule_emp, nom_emp, prenom_emp FROM Employe ORDER BY nom_emp");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBulletinEnregistre(string $matricule, int $mois, int $annee): array|false
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT
            e.nom_emp  AS nom,
            e.prenom_emp  AS prenom,
            e.matricule_emp AS matricule,
            e.code_dept  AS departement,
            b.salaire_brut AS salaire_brut,
            b.nbr_jours_travaille AS nb_jours,
            b.nbr_abs_injustifie  AS nb_absences
        FROM Bulletin b
        JOIN Employe e ON e.matricule_emp = b.matricule_emp
        WHERE b.matricule_emp = :mat
          AND b.mois  = :mois
          AND b.annee = :annee
        LIMIT 1
    ");
    $stmt->execute([':mat' => $matricule, ':mois' => $mois, ':annee' => $annee]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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

        // On va chercher le bulletin déjà enregistré pour cette période
        $b = getBulletinEnregistre($matricule, $mois, $annee);

        if (!$b) {
            $message = "Aucun bulletin enregistré pour cet employé sur cette période.";
        } else {
            $salaire_brut = (float) $b['salaire_brut'];
            $nb_jours     = (int) $b['nb_jours'];
            $nb_absences  = (int) $b['nb_absences'];

            // Retenues et salaire net : données calculées à l'affichage
            $retenue_unitaire = round($salaire_brut / 22, 2);
            $retenues_total   = round($retenue_unitaire * $nb_absences, 2);
            $salaire_net      = round($salaire_brut - $retenues_total, 2);

            $message = "Bulletin affiché.";
            $bulletin = [
                'nom'             => $b['nom'],
                'prenom'          => $b['prenom'],
                'matricule'       => $b['matricule'],
                'departement'     => $b['departement'],
                'mois'            => $mois,
                'annee'           => $annee,
                'salaire_brut'    => $salaire_brut,
                'nb_jours'        => $nb_jours,
                'nb_absences'     => $nb_absences,
                'retenue_unit'    => $retenue_unitaire,
                'retenues_total'  => $retenues_total,
                'salaire_net'     => $salaire_net,
            ];
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
    <title>Afficher un Bulletin de Paie</title>
    <style>
        #bulletin-affiche {
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
        #bulletin-affiche .bulletin-ligne {
            display: block ;
            text-align: center;
        }
        #bulletin-affiche .bulletin-ligne span {
            display: inline;
        }
        #bulletin-affiche .bulletin-total {
            display: block;
            text-align: center;
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

<!-- formulaire -->
<form method="post" action="" class="form-container" id="generer-bulletin-form">
    <h2>Afficher un Bulletin de Paie</h2>

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

    <input type="submit" value="Afficher le bulletin" name="generer_bulletin">
</form>

<!-- bulletin de paieaffiché -->
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

<!-- historique des bulletins -->
<div class="result-container" id="historique-bulletins">
    <h2>Historique des bulletins</h2>

    <?php if (empty($bulletins)) : ?>
        <p>Aucun bulletin enregistré pour l'instant.</p>
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
                    // Retenues et salaire net sont calculés à l'affichage
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