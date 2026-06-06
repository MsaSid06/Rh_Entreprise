<?php
require_once "Read.php";
require_once "insert.php";


?>
<?php
function display(String $matricule_emp)
{ ?>


<br>
<h3 class="titre"> INFORMATION DE L'EMPLOYÉ
    <?php echo $matricule_emp; ?></h3> <br>
<?php $r = displayEmploye($matricule_emp);

    if ($r == false) {
        echo "Aucun employé trouvé avec le matricule $matricule_emp.";
    } ?><br>
<hr><br>

<h3 class="titre">INFORMATION DU CONTRAT DE L'EMPLOYÉ
    <?php echo $matricule_emp; ?>
</h3><br>
<?php if (!displayContrat($matricule_emp)) {
    echo "Pas de contrat pour l'employe $matricule_emp";
}; ?><br>
<hr><br>

<h3 class="titre">INFORMATION DU CONGE DE L'EMPLOYÉ
    <?php echo $matricule_emp; ?>
</h3>
<br>
<?php if (!displayConges($matricule_emp)) {
    echo "Pas de conge pour l'employer $matricule_emp";
}; ?><br>
<hr><br>

<h3 class="titre">INFORMATION DES PRESENCES DE L'EMPLOYÉ
    <?php echo $matricule_emp; ?>
</h3><br>
<?php if (!displayAbsences($matricule_emp)) {
    echo "Pas de presence ni d'absence pour l'employe $matricule_emp";
}; ?><br>
<hr><br>
<?php }

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>INFORMATION D'UN EMPLOYÉ</title>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
            </ul>
        </nav>
    </header>
    <form method="post" action="" class="form-container">
        <label for="matricule_emp">Matricule de l'employé:</label>
        <input type="text" id="matricule_emp" name="matricule_emp" required>
        <input type="submit" value="Afficher les informations">
    </form>
    <div class="result-container">
        <?php
            if (isset($_POST['matricule_emp'])) {
                $matricule_emp = strtoupper($_POST['matricule_emp']);
                display($matricule_emp);
            }
?>
    </div>

</body>

</html>

<!-- http://localhost/TP_BD/test.php -->