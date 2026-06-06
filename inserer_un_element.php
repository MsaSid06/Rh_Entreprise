<?php
require_once "insert.php";


function ajouterEmploye()
{
    if (isset($_POST['matricule_emp'], $_POST['nom_emp'], $_POST['prenom_emp'], $_POST['date_naissance'], $_POST['email'], $_POST['date_embauche'], $_POST['code_poste'], $_POST['code_dept'])) {
        $matricule_emp = strtoupper($_POST['matricule_emp']);
        $nom_emp = strtoupper($_POST['nom_emp']);
        $prenom_emp = ucfirst(strtolower($_POST['prenom_emp']));
        $date_naissance = $_POST['date_naissance'];
        $email = $_POST['email'];
        $date_embauche = $_POST['date_embauche'];
        $code_poste = strtoupper($_POST['code_poste']);
        $code_dept = strtoupper($_POST['code_dept']);
        $telephone = $_POST['telephone'];
        $return = insertEmploye($matricule_emp, $nom_emp, $prenom_emp, $date_naissance, $email, $telephone, $date_embauche, $code_poste, $code_dept);
        if ($return === true) {
            echo "<p style='color: green;'>Employé ajouté avec succès.</p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'ajout de l'employé : $return</p>";
        }
    }
    return false;
}


function ajouterContrat()
{
    if (isset($_POST['matricule_emp_contrat'], $_POST['type_contrat'], $_POST['date_debut_contrat'], $_POST['date_fin_contrat'], $_POST['salaire_brut'], $_POST['est_actif'])) {
        $matricule_emp = strtoupper($_POST['matricule_emp_contrat']);
        $type_contrat = $_POST['type_contrat'];
        $date_debut_contrat = $_POST['date_debut_contrat'];
        $date_fin_contrat = $_POST['date_fin_contrat'];
        $salaire_brut = floatval($_POST['salaire_brut']);
        $est_actif = boolval($_POST['est_actif']);
        $return = insertContrat($matricule_emp, $type_contrat, $date_debut_contrat, $date_fin_contrat, $salaire_brut, $est_actif);
        if ($return === true) {
            echo "<p style='color: green;'>Contrat ajouté avec succès.</p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'ajout du contrat : $return</p>";
        }
    }
    return false;
}

function ajouterConge()
{
    if (isset($_POST['matricule_emp_conge'], $_POST['date_debut_conge'], $_POST['date_fin_conge'], $_POST['type_conge'], $_POST['nb_jour_restant'], $_POST['statut_conge'])) {
        $matricule_emp = strtoupper($_POST['matricule_emp_conge']);
        $date_debut_conge = $_POST['date_debut_conge'];
        $date_fin_conge = $_POST['date_fin_conge'];
        $type_conge = $_POST['type_conge'];
        $nb_jour_restant = intval($_POST['nb_jour_restant']);
        $statut_conge = $_POST['statut_conge'];

        $return = insertConge($matricule_emp, $type_conge, $statut_conge, $date_debut_conge, $date_fin_conge, $nb_jour_restant);
        if ($return === true) {
            echo "<p style='color: green;'>Congé ajouté avec succès.</p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'ajout du congé : $return</p>";
        }
    }
    return false;
}

function ajouterPresence()
{
    if (isset($_POST['matricule_emp_presence'], $_POST['heure_arrive'], $_POST['heure_depart'], $_POST['date_presence'], $_POST['statut_presence'])) {
        $matricule_emp = strtoupper($_POST['matricule_emp_presence']);
        $heure_arrive = $_POST['heure_arrive'];
        $heure_depart = $_POST['heure_depart'];
        $date_presence = $_POST['date_presence'];
        $statut_presence = $_POST['statut_presence'];

        $return = insertPresence($matricule_emp, $heure_arrive, $heure_depart, $date_presence, $statut_presence);
        if ($return === true) {
            $_POST['reponse'] = "<p style='color: green;'>Présence ajoutée avec succès.</p>";
        } else {
            $_POST['reponse'] = "<p style='color: red;'>Erreur lors de l'ajout de la présence : $return</p>";
        }
    }
    return false;
}

$message = "";

if (isset($_POST['ajouter_employe'])) {
    $return = ajouterEmploye();

    $message = $return
        ? "Employé ajouté avec succès."
        : "Erreur lors de l'ajout de l'employé : $return";
}

if (isset($_POST['ajouter_contrat'])) {
    $return = ajouterContrat();

    $message = $return
        ? "Contrat ajouté avec succès."
        : "Erreur lors de l'ajout du contrat : $return";
}

if (isset($_POST['ajouter_conge'])) {
    $return = ajouterConge();

    $message = $return
        ? "Congé ajouté avec succès."
        : "Erreur lors de l'ajout du congé : $return";
}

if (isset($_POST['ajouter_presence'])) {
    $return = ajouterPresence();

    $message = $return
        ? "Présence ajoutée avec succès."
        : "Erreur lors de l'ajout de la présence : $return";
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>Inserer un element</title>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#ajouter-employe-form">Ajouter un employé</a></li>
                <li><a href="#ajouter-contrat-form">Ajouter un Contrat</a></li>
                <li><a href="#ajouter-conge-form">Ajouter un Congé</a></li>
                <li><a href="#ajouter-presence-form">Ajouter une Présence</a></li>
            </ul>
        </nav>
        <h4 style="color: #e9ff6a; margin-top: 20px;">Tout les champs sont obligatoires</h4>
        <?php if (!empty($message)) : ?>
        <script>
            alert("<?php echo $message; ?>");
        </script>
        <?php endif; ?>
    </header>
    <form method="post" action="" class="form-container" id="ajouter-employe-form">
        <h2>Ajouter un employé</h2>
        <label for="matricule_emp">Matricule de l'employé:</label>
        <input type="text" id="matricule_emp" name="matricule_emp" required><br>

        <label for="nom_emp">Nom de l'employé:</label>
        <input type="text" id="nom_emp" name="nom_emp" required><br>

        <label for="prenom_emp">Prénom de l'employé:</label>
        <input type="text" id="prenom_emp" name="prenom_emp" required><br>

        <label for="date_naissance">Date de naissance de l'employé:</label>
        <input type="date" id="date_naissance" name="date_naissance" required><br>

        <label for="email">Email de l'employé:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="date_embauche">Date d'embauche de l'employé:</label>
        <input type="date" id="date_embauche" name="date_embauche" required><br>

        <label for="telephone">Téléphone de l'employé:</label>
        <input type="text" id="telephone" name="telephone" required><br>

        <label for="code_poste">Code du poste de l'employé:</label>
        <input type="text" id="code_poste" name="code_poste" required><br>

        <label for="code_dept">Code du département de l'employé:</label>
        <input type="text" id="code_dept" name="code_dept" required><br>

        <input type="submit" value="Ajouter l'employé" name="ajouter_employe">
    </form>

    <form method="post" action=" " class="form-container" id="ajouter-contrat-form">
        <h2>Ajouter un contrat</h2>

        <label for="matricule_emp_contrat">Matricule de l'employé:</label>
        <input type="text" id="matricule_emp_contrat" name="matricule_emp_contrat" required><br>

        <label for="type_contrat">Type de contrat:</label>
        <select id="type_contrat" name="type_contrat" required>
            <option value="CDD">CDD</option>
            <option value="CDI">CDI</option>
            <option value="Stage">Stage</option>
        </select><br>

        <label for="date_debut_contrat">Date de début du contrat:</label>
        <input type="date" id="date_debut_contrat" name="date_debut_contrat" required><br>

        <label for="date_fin_contrat">Date de fin du contrat:</label>
        <input type="date" id="date_fin_contrat" name="date_fin_contrat" required><br>

        <label for="salaire_brut">Salaire brut:</label>
        <input type="number" id="salaire_brut" name="salaire_brut" step="0.01" required><br>

        <label for="est_actif">Est actif:</label>
        <select id="est_actif" name="est_actif" required>
            <option value="1">Oui</option>
            <option value="0">Non</option>
        </select><br>

        <input type="submit" value="Ajouter le contrat" name="ajouter_contrat">
    </form>

    <form method="post" action="" class="form-container" id="ajouter-conge-form">
        <h2>Ajouter un congé</h2>

        <label for="matricule_emp_conge">Matricule de l'employé:</label>
        <input type="text" id="matricule_emp_conge" name="matricule_emp_conge" required><br>

        <label for="type_conge">Type de congé:</label>
        <select id="type_conge" name="type_conge" required>
            <option value="Annuel">Annuel</option>
            <option value="Maladie">Maladie</option>
            <option value="maternite">maternité</option>
        </select><br>

        <label for="date_debut_conge">Date de début du congé:</label>
        <input type="date" id="date_debut_conge" name="date_debut_conge" required><br>

        <label for="date_fin_conge">Date de fin du congé:</label>
        <input type="date" id="date_fin_conge" name="date_fin_conge" required><br>

        <label for="nb_jour_restant">Nombre de jours restants:</label>
        <input type="number" id="nb_jour_restant" name="nb_jour_restant" required><br>

        <label for="statut_conge">Statut du congé:</label>
        <select id="statut_conge" name="statut_conge" required>
            <option value="demande">En attente</option>
            <option value="approuve">Approuvé</option>
            <option value="refuse">Refusé</option>
        </select><br>

        <input type="submit" value="Ajouter le congé" name="ajouter_conge">
    </form>

    <form method="post" action=" " class="form-container" id="ajouter-presence-form">
        <h2>Ajouter une présence</h2>

        <label for="matricule_emp_presence">Matricule de l'employé:</label>
        <input type="text" id="matricule_emp_presence" name="matricule_emp_presence" required><br>

        <label for="heure_arrive">Heure d'arrivée:</label>
        <input type="time" id="heure_arrive" name="heure_arrive" required><br>

        <label for="heure_depart">Heure de départ:</label>
        <input type="time" id="heure_depart" name="heure_depart" required><br>

        <label for="date_presence">Date de présence:</label>
        <input type="date" id="date_presence" name="date_presence" required><br>

        <label for="statut_presence">Statut de présence:</label>
        <select id="statut_presence" name="statut_presence" required>
            <option value="present">Présent</option>
            <option value="absent">Absent</option>
            <option value="Conge">Congé</option>
        </select><br>

        <input type="submit" value="Ajouter la présence" name="ajouter_presence">
    </form>

</body>

</html>

<!-- http://localhost/TP_BD/inserer_un_element.php -->