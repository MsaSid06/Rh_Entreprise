<?php
require_once "update.php";


function modifierEmploye()
{
    if (isset($_POST['nouveau_matricule_emp'], $_POST['nouveau_nom_emp'], $_POST['nouveau_prenom_emp'], $_POST['nouvelle_date_naissance'], $_POST['nouveau_email'], $_POST['nouvelle_date_embauche'], $_POST['nouveau_code_poste'], $_POST['nouveau_code_dept'])) {
        $matricule_emp = strtoupper($_POST['nouveau_matricule_emp']);
        $nom_emp = strtoupper($_POST['nouveau_nom_emp']);
        $prenom_emp = ucfirst(strtolower($_POST['nouveau_prenom_emp']));
        $date_naissance = $_POST['nouvelle_date_naissance'];
        $email = $_POST['nouveau_email'];
        $date_embauche = $_POST['nouvelle_date_embauche'];
        $code_poste = strtoupper($_POST['nouveau_code_poste']);
        $code_dept = strtoupper($_POST['nouveau_code_dept']);
        $telephone = $_POST['nouveau_telephone'];
        $return = updateEmploye($matricule_emp, $nom_emp, $prenom_emp, $date_naissance, $email, $telephone, $date_embauche, $code_poste, $code_dept);
        return $return;
    }
    return false;
}


function modifierContrat()
{
    if (isset($_POST['nouveau_matricule_emp_contrat'], $_POST['nouveau_type_contrat'], $_POST['nouvelle_date_debut_contrat'], $_POST['nouvelle_date_fin_contrat'], $_POST['nouveau_salaire_brut'], $_POST['nouveau_est_actif'])) {
        $matricule_emp = strtoupper($_POST['nouveau_matricule_emp_contrat']);
        $type_contrat = $_POST['nouveau_type_contrat'];
        $date_debut_contrat = $_POST['nouvelle_date_debut_contrat'];
        $date_fin_contrat = $_POST['nouvelle_date_fin_contrat'];
        $salaire_brut = floatval($_POST['nouveau_salaire_brut']);
        $est_actif = boolval($_POST['nouveau_est_actif']);
        $return = updateContrat($matricule_emp, $type_contrat, $date_debut_contrat, $date_fin_contrat, $salaire_brut, $est_actif);
        return $return;
    }
    return false;
}

function modifierConge()
{
    if (isset($_POST['nouveau_matricule_emp_conge'], $_POST['nouvelle_date_debut_conge'], $_POST['nouvelle_date_fin_conge'], $_POST['nouveau_type_conge'], $_POST['nouveau_nb_jour_restant'], $_POST['nouveau_statut_conge'])) {
        $matricule_emp = strtoupper($_POST['nouveau_matricule_emp_conge']);
        $date_debut_conge = $_POST['nouvelle_date_debut_conge'];
        $date_fin_conge = $_POST['nouvelle_date_fin_conge'];
        $type_conge = $_POST['nouveau_type_conge'];
        $nb_jour_restant = intval($_POST['nouveau_nb_jour_restant']);
        $statut_conge = $_POST['nouveau_statut_conge'];

        $return = updateConge($matricule_emp, $type_conge, $statut_conge, $date_debut_conge, $date_fin_conge, $nb_jour_restant);

        return $return;
    }
    return false;
}

function modifierPresence()
{
    if (isset($_POST['nouveau_matricule_emp_presence'], $_POST['nouvelle_heure_arrive'], $_POST['nouvelle_heure_depart'], $_POST['nouvelle_date_presence'], $_POST['nouveau_statut_presence'])) {
        $matricule_emp = strtoupper($_POST['nouveau_matricule_emp_presence']);
        $heure_arrive = $_POST['nouvelle_heure_arrive'];
        $heure_depart = $_POST['nouvelle_heure_depart'];
        $date_presence = $_POST['nouvelle_date_presence'];
        $statut_presence = $_POST['nouveau_statut_presence'];

        $return = updatePresence($matricule_emp, $heure_arrive, $heure_depart, $date_presence, $statut_presence);

        return $return;
    }
    return false;
}
$message = "";

if(isset($_POST['modifier_employe'])) {
    $return = modifierEmploye();
    $message = $return === true ? "Employé modifié avec succès." : "Erreur lors de la modification de l'employé : $return";
}
if(isset($_POST['modifier_contrat'])) {
    $return = modifierContrat();
    $message = $return === true ? "Contrat modifié avec succès." : "Erreur lors de la modification du contrat : $return";
}
if(isset($_POST['modifier_conge'])) {
    $return = modifierConge();
    $message = $return === true ? "Congé modifié avec succès." : "Erreur lors de la modification du congé : $return";
}
if(isset($_POST['modifier_presence'])) {
    $return = modifierPresence();
    $message = $return === true ? "Présence modifiée avec succès." : "Erreur lors de la modification de la présence : $return";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Modifier un element</title>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#modifier-employe-form">Modifier un employé</a></li>
                <li><a href="#modifier-contrat-form">Modifier un Contrat</a></li>
                <li><a href="#modifier-conge-form">Modifier un Congé</a></li>
                <li><a href="#modifier-presence-form">Modifier une Présence</a></li>
            </ul>
        </nav>
        <h4 style="color: #ecfe4d; margin-top: 20px;">Remplissez tout les champs que vous souhaitez modifier et mettez
            les
            anciens
            informations dans les champs
            que vous ne souhaitez pas modifier</h4>
        <?php if (!empty($message)) : ?>
        <script>
            alert("<?php echo $message; ?>");
        </script>
        <?php endif; ?>
    </header>
    <form method="post" action="" class="form-container" id="modifier-employe-form">
        <h2>Modifier un employé</h2>

        <label for="matricule_emp">Matricule de l'employé à modifier:</label>
        <input type="text" id="matricule_emp" name="nouveau_matricule_emp" required><br>

        <label for="nom_emp">Nouveau Nom de l'employé:</label>
        <input type="text" id="nom_emp" name="nouveau_nom_emp" required><br>

        <label for="prenom_emp">Nouveau Prénom de l'employé:</label>
        <input type="text" id="prenom_emp" name="nouveau_prenom_emp" required><br>

        <label for="date_naissance">Nouvelle Date de naissance de l'employé:</label>
        <input type="date" id="date_naissance" name="nouvelle_date_naissance" required><br>

        <label for="email">Nouvel Email de l'employé:</label>
        <input type="email" id="email" name="nouveau_email" required><br>

        <label for="date_embauche">Nouvelle Date d'embauche de l'employé:</label>
        <input type="date" id="date_embauche" name="nouvelle_date_embauche" required><br>

        <label for="telephone">Nouveau Téléphone de l'employé:</label>
        <input type="text" id="telephone" name="nouveau_telephone" required><br>

        <label for="code_poste">Nouveau Code du poste de l'employé:</label>
        <input type="text" id="code_poste" name="nouveau_code_poste" required><br>

        <label for="code_dept">Nouveau Code du département de l'employé:</label>
        <input type="text" id="code_dept" name="nouveau_code_dept" required><br>

        <input type="submit" value="Modifier l'employé" name="modifier_employe">
    </form>

    <form method="post" action="" class="form-container" id="modifier-contrat-form">
        <h2>Modifier un contrat</h2>

        <label for="matricule_emp_contrat">Matricule de l'employé dont le contrat doit être modifié:</label>
        <input type="text" id="matricule_emp_contrat" name="nouveau_matricule_emp_contrat" required><br>

        <label for="type_contrat">Nouveau Type de contrat:</label>
        <select id="type_contrat" name="nouveau_type_contrat" required>
            <option value="CDD">CDD</option>
            <option value="CDI">CDI</option>
            <option value="Stage">Stage</option>
        </select><br>

        <label for="date_debut_contrat">Nouvelle Date de début du contrat:</label>
        <input type="date" id="date_debut_contrat" name="nouvelle_date_debut_contrat" required><br>

        <label for="date_fin_contrat">Nouvelle Date de fin du contrat:</label>
        <input type="date" id="date_fin_contrat" name="nouvelle_date_fin_contrat" required><br>

        <label for="salaire_brut">Nouveau Salaire brut:</label>
        <input type="number" id="salaire_brut" name="nouveau_salaire_brut" step="0.01" required><br>

        <label for="est_actif">Nouveau Statut:</label>
        <select id="est_actif" name="nouveau_est_actif" required>
            <option value="1">Oui</option>
            <option value="0">Non</option>
        </select><br>

        <input type="submit" value="Modifier le contrat" name="modifier_contrat">
    </form>

    <form method="post" action=" " class="form-container" id="modifier-conge-form">
        <h2>Modifier un congé</h2>

        <label for="matricule_emp_conge">Matricule de l'employé dont le congé doit être modifié:</label>
        <input type="text" id="matricule_emp_conge" name="nouveau_matricule_emp_conge" required><br>

        <label for="type_conge">Nouveau Type de congé:</label>
        <select id="type_conge" name="nouveau_type_conge" required>
            <option value="Annuel">Annuel</option>
            <option value="Maladie">Maladie</option>
            <option value="maternite">maternité</option>
        </select><br>

        <label for="date_debut_conge">Nouvelle Date de début du congé:</label>
        <input type="date" id="date_debut_conge" name="nouvelle_date_debut_conge" required><br>

        <label for="date_fin_conge">Nouvelle Date de fin du congé:</label>
        <input type="date" id="date_fin_conge" name="nouvelle_date_fin_conge" required><br>

        <label for="nb_jour_restant">Nouveau Nombre de jours restants:</label>
        <input type="number" id="nb_jour_restant" name="nouveau_nb_jour_restant" required><br>

        <label for="statut_conge">Nouveau Statut du congé:</label>
        <select id="statut_conge" name="nouveau_statut_conge" required>
            <option value="demande">En attente</option>
            <option value="approuve">Approuvé</option>
            <option value="refuse">Refusé</option>
        </select><br>

        <input type="submit" value="Modifier le congé" name="modifier_conge">
    </form>

    <form method="post" action=" " class="form-container" id="modifier-presence-form">
        <h2>Modifier une présence</h2>

        <label for="matricule_emp_presence">Matricule de l'employé dont la présence doit être modifiée:</label>
        <input type="text" id="matricule_emp_presence" name="nouveau_matricule_emp_presence" required><br>

        <label for="heure_arrive">Heure d'arrivée:</label>
        <input type="time" id="heure_arrive" name="nouvelle_heure_arrive" required><br>

        <label for="heure_depart">Heure de départ:</label>
        <input type="time" id="heure_depart" name="nouvelle_heure_depart" required><br>

        <label for="date_presence">Date de présence:</label>
        <input type="date" id="date_presence" name="nouvelle_date_presence" required><br>

        <label for="statut_presence">Nouveau Statut de présence:</label>
        <select id="statut_presence" name="nouveau_statut_presence" required>
            <option value="present">Présent</option>
            <option value="absent">Absent</option>
            <option value="Conge">Congé</option>
        </select><br>

        <input type="submit" value="Modifier la présence" name="modifier_presence">
    </form>

</body>

</html>

<!-- http://localhost/TP_BD/Modifier_les_infos.php -->