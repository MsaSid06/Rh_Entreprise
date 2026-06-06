<?php
require_once "./Read.php";
require_once "./insert.php";

function ajouterPresence()
{
    if (isset($_POST['matricule_emp_presence_ajout'], $_POST['heure_arrive_ajout'], $_POST['heure_depart_ajout'], $_POST['date_presence_ajout'], $_POST['statut_presence_ajout'])) {
        $matricule_emp = strtoupper($_POST['matricule_emp_presence_ajout']);
        $heure_arrive = $_POST['heure_arrive_ajout'];
        $heure_depart = $_POST['heure_depart_ajout'];
        $date_presence = $_POST['date_presence_ajout'];
        $statut_presence = $_POST['statut_presence_ajout'];

        $return = insertPresence($matricule_emp, $heure_arrive, $heure_depart, $date_presence, $statut_presence);
        return $return;
    }
    return false;
}?>


<?php
if (isset($_POST['ajouter_presence_dep'])) {

    $return = ajouterPresence();

    $message = $return
        ? "Présence ajoutée avec succès, veuillez continuer avec les autres employés !"
        : "Erreur lors de l'ajout de la présence.";

    ?>
<script>
    alert( <?= json_encode($message) ?> );
</script>
<?php
}
?>

<?php

    function employer_du_dep()
    {
        if (isset($_POST['departement'])) {
            $users = getEmployes();
            $employe_dep = [];
            foreach ($users as $u) {
                if ($u['code_dept'] == $_POST['departement']) {
                    $employe_dep[] = $u;
                }

            }
            return $employe_dep;
        }
        return false;
    }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisir les presences/absences Departement </title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
            </ul>
        </nav>
    </header>
    <form method="post" class="form-container">
        <label for="code_dep">Veuillez choisir le departement pour lequel vous
            voulez ajoutez des
            presences/absences</label>
        <select name="departement" id="code_dep" required>
            <option value="D001">D001</option>
            <option value="D002">D002</option>
            <option value="D003">D003</option>
            <option value="D004">D004</option>

            <input type="submit" value="Choisir ce departement" name="choix_dep">

        </select></br>

    </form>


    <?php if (isset($_POST['choix_dep'])): ?>
    <form method="post" action=" " class="form-container" id="ajouter-presence-form">
        <h2>Ajouter une présence</h2>

        <label for="matricule_emp_presence">Choisir les Matricule des employés du departement:</label>
        <select name="matricule_emp_presence_ajout" id="matricule_emp_presence">
            <?php
                $employes = employer_du_dep();
        foreach ($employes as $emp) {
            echo "<option value='{$emp['matricule_emp']}' name='matricule_choisi'>{$emp['matricule_emp']}</option>";

        }
        ?>
        </select>

        <!-- <input type="text" id="matricule_emp_presence" name="matricule_emp_presence_ajout" required><br> -->

        <label for="heure_arrive">Heure d'arrivée:</label>
        <input type="time" id="heure_arrive" name="heure_arrive_ajout" required><br>

        <label for="heure_depart">Heure de départ:</label>
        <input type="time" id="heure_depart" name="heure_depart_ajout" required><br>

        <label for="date_presence">Date de présence:</label>
        <input type="date" id="date_presence" name="date_presence_ajout" required><br>

        <label for="statut_presence">Statut de présence:</label>
        <select id="statut_presence" name="statut_presence_ajout" required>
            <option value="present">Présent</option>
            <option value="absent">Absent</option>
            <option value="Conge">Congé</option>
        </select><br>

        <input type="submit" value="Ajouter la présence" name="ajouter_presence_dep">
    </form>
    <?php endif;?>

</body>

<!-- <script>
    const form = document.getElementById("ajouter-presence-form");

    form.addEventListener("submit", (e) => {
        e.preventDefault();
    })
</script> -->

</html>


<!-- http://localhost/TP_BD/ -->