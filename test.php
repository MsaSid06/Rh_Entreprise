<?php
require_once "Read.php";
require_once "insert.php";


?>
<?php
function display(String $matricule_emp)
{ ?>
INFORMATION DE L'EMPLOYÉ <?php echo $matricule_emp; ?> : <br>
<?php displayEmploye($matricule_emp); ?><br>
<hr>
INFORMATION Du CONTRAT DE L'EMPLOYÉ <?php echo $matricule_emp; ?>
: <br>
<?php displayContrat($matricule_emp); ?><br>
<hr>
INFORMATION DU CONGE DE L'EMPLOYÉ <?php echo $matricule_emp; ?> :
<br>
<?php displayConges($matricule_emp); ?><br>
<hr>
INFORMATION DES PRESENCES DE L'EMPLOYÉ
<?php echo $matricule_emp; ?> : <br>
<?php displayAbsences($matricule_emp); ?><br>
<hr>
<?php } ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>INFORMATION D'UN EMPLOYÉ</title>
</head>

<body>
    <form method="post" action="" class="form-container">
        <label for="matricule_emp">Matricule de l'employé:</label>
        <input type="text" id="matricule_emp" name="matricule_emp" required>
        <input type="submit" value="Afficher les informations">
    </form>
    <?php
    if (isset($_POST['matricule_emp'])) {
        $matricule_emp = strtoupper($_POST['matricule_emp']);
        display($matricule_emp);
    }
?>

</body>

</html>

<!-- http://localhost/TP_BD/test.php -->