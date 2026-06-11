<?php

require_once 'Read.php';

function bulletin_employer(string $matricule, int  $mois, int $anne)
{
    $bull_dispo = getBulletins();

    foreach ($bull_dispo as $b) {
        if ($b['matricule_emp'] == $matricule && $b['mois'] == $mois && $b['annee'] == $anne) {
            return $b;
        }
    }
    return -1;
}



$message = "";
$bulletin = null;

$mois_noms = [
    1 => 'Janvier', 2 => 'Février', 3 => 'Mars',     4 => 'Avril',
    5 => 'Mai',     6 => 'Juin',    7 => 'Juillet',   8 => 'Août',
    9 => 'Septembre',10 => 'Octobre',11 => 'Novembre',12 => 'Décembre',
];

$employes = getEmployes();

if (isset($_POST['generer_bulletin'])) {

    $matricule = $_POST['matricule_emp_bulletin'];
    $mois = (int) $_POST['mois_bulletin'];
    $annee = (int) $_POST['annee_bulletin'];

    global $bulletin;

    $b = bulletin_employer($matricule, $mois, $annee);

    if ($b == -1) {

        echo '<script>
                alert("Pas de bulletin disponible pour cette période. Veuillez renseigner les bonnes valeurs !");
              </script>';

    } else {

        $employes = getEmployes();

        foreach ($employes as $emp) {

            if ($emp['matricule_emp'] == $matricule) {

                $b["code_dept"] = $emp["code_dept"];
                $b["code_poste"] = $emp["code_poste"];
                $b["nom_emp"] = $emp["nom_emp"];
                $b["prenom_emp"] = $emp["prenom_emp"];

                $bulletin = $b;

                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Générer un Bulletin de Paie</title>
</head>

<body>
    <?php
        include './header.php';
?>

    <!-- formulaire -->
    <form method="post" action="" class="form-container" id="generer-bulletin-form">
        <h2>Générer un Bulletin de Paie</h2>

        <label for="matricule_emp_bulletin">Employé :</label>
        <select id="matricule_emp_bulletin" name="matricule_emp_bulletin" required>
            <option value="">-- Choisir un employé --</option>
            <?php
            foreach ($employes as $emp) {
                echo "<option value='{$emp['matricule_emp']}' name='matricule_choisi'>{$emp['matricule_emp']} -- {$emp['nom_emp']}  {$emp['nom_emp']} </option>";
            }
?>
        </select>

        <label for="mois_bulletin">Mois :</label>
        <select id="mois_bulletin" name="mois_bulletin" required>
            <?php foreach ($mois_noms as $num => $nom) : ?>
            <option value="<?= $num ?>">
                <?= $nom ?>
            </option>
            <?php endforeach; ?>
        </select><br>

        <label for="annee_bulletin">Année :</label>
        <input type="number" id="annee_bulletin" name="annee_bulletin"
            value="<?= isset($_POST['annee_bulletin']) ? (int)$_POST['annee_bulletin'] : date('Y') ?>"
            min="1999" max="2026" required><br>

        <input type="submit" value="Générer le bulletin" name="generer_bulletin">
    </form>

    <!-- bulletin généré -->
    <?php if ($bulletin) : ?>

    <?php
$retenues = $bulletin['salaire_brut'] / 22;
        $total_retenues = $retenues * $bulletin['nbr_abs_injustifie'];
        $net = $bulletin['salaire_brut'] - $total_retenues;
        ?>

    <div class="bulletin-paie" id="bulletin-affiche">
        <h3>Bulletin de Paie</h3>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <tr>
                <th colspan="2">Informations de l'employé</th>
            </tr>

            <tr>
                <td><strong>Employé</strong></td>
                <td><?= $bulletin['nom_emp'] . ' ' . $bulletin['prenom_emp'] ?>
                </td>
            </tr>

            <tr>
                <td><strong>Matricule</strong></td>
                <td><?= $bulletin['matricule_emp'] ?>
                </td>
            </tr>

            <tr>
                <td><strong>Département</strong></td>
                <td><?= $bulletin['code_dept'] ?>
                </td>
            </tr>

            <tr>
                <td><strong>Poste</strong></td>
                <td><?= $bulletin['code_poste'] ?>
                </td>
            </tr>

            <tr>
                <td><strong>Période</strong></td>
                <td><?= $mois_noms[$bulletin['mois']] . ' ' . $bulletin['annee'] ?>
                </td>
            </tr>

            <tr>
                <th colspan="2">Détails de la paie</th>
            </tr>

            <tr>
                <td><strong>Salaire brut</strong></td>
                <td><?= number_format($bulletin['salaire_brut'], 2, ',', ' ') ?>
                    EURO</td>
            </tr>

            <tr>
                <td><strong>Jours travaillés</strong></td>
                <td><?= $bulletin['nbr_jours_travaille'] ?>
                    jour(s)</td>
            </tr>

            <tr>
                <td><strong>Absences injustifiées</strong></td>
                <td><?= $bulletin['nbr_abs_injustifie'] ?>
                    jour(s)</td>
            </tr>

            <tr>
                <td style="color: #c62828;"><strong>Retenue par jour d'absence</strong></td>
                <td style="color: #c62828;">-
                    <?= number_format($retenues, 2, ',', ' ') ?>
                    EURO / jour
                </td>
            </tr>

            <tr class="retenue">
                <td><strong>Total retenues</strong></td>
                <td>-
                    <?= number_format($total_retenues, 2, ',', ' ') ?>
                    EURO
                </td>
            </tr>

            <tr class="net">
                <th>SALAIRE NET À PAYER</th>
                <th><?= number_format($net, 2, ',', ' ') ?>
                    EURO</th>
            </tr>
        </table>
    </div>

    <?php endif; ?>
</body>

</html>