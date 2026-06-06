<?php

require_once "config/database.php";

function getEmployes()
{
    global $pdo;
    try {
        $sql = "SELECT * FROM Employe";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;

    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}

function getContrats()
{
    global $pdo;
    try {
        $sql = "SELECT * FROM Contrat";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $contrats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $contrats;

    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}


function getConges()
{
    global $pdo;
    try {
        $sql = "SELECT * FROM Conge";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $conges;

    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}


function getPresences()
{
    global $pdo;
    try {
        $sql = "SELECT * FROM Presence";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $presences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $presences;

    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}

function getDepartements()
{
    global $pdo;
    try {
        $sql = "SELECT * FROM Departement";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $departements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $departements;

    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}


function getPostes()
{
    global $pdo;
    try {
        $sql = "SELECT * FROM Poste";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $postes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $postes;

    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}

function getBulletins()
{
    global $pdo;
    try {
        $sql = "SELECT * FROM Bulletin";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $bulletins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $bulletins;

    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}

// http://localhost/TP_BD/config/database.php

function displayEmploye(String $matricule_emp)
{
    $users = getEmployes();
    if (!$users) {
        return false ;
    }
    foreach ($users as $user) {
        if ($user['matricule_emp'] == $matricule_emp) {

            echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
            <tr>
                <th style='background-color: #f2f2f2;'>Champ</th>
                <th style='background-color: #f2f2f2;'>Valeur</th>
            </tr>
            <tr>
                <td><strong>Matricule</strong></td>
                <td>" . $user['matricule_emp'] . "</td>
            </tr>
            <tr>
                <td><strong>Nom</strong></td>
                <td>" . $user['nom_emp'] . "</td>
            </tr>
            <tr>
                <td><strong>Prénom</strong></td>
                <td>" . $user['prenom_emp'] . "</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>" . $user['email'] . "</td>
            </tr>
            <tr>
                <td><strong>Date de naissance</strong></td>
                <td>" . $user['date_naiss'] . "</td>
            </tr>
            <tr>
                <td><strong>Date d'embauche</strong></td>
                <td>" . $user['date_embauche'] . "</td>
            </tr>
            <tr>
                <td><strong>Téléphone</strong></td>
                <td>" . $user['telephone'] . "</td>
            </tr>
            </table>";
            return true ;
        }
    }
    return false;
}

function displayContrat(String $matricule_emp)
{
    $contrats = getContrats();
    if (!$contrats) {
        return false;
    }

    foreach ($contrats as $contrat) {
        if ($contrat['matricule_emp'] == $matricule_emp) {

            echo
            "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
                <tr>
                    <th style='background-color: #f2f2f2;'>Champ</th>
                    <th style='background-color: #f2f2f2;'>Valeur</th>
                </tr>
                <tr>
                    <td><strong>Numéro du contrat</strong></td>
                    <td>" . $contrat['id_contrat'] . "</td>
                </tr>
                <tr>
                    <td><strong>Type du contrat</strong></td>
                    <td>" . $contrat['type_contrat'] . "</td>
                </tr>
                <tr>
                    <td><strong>Date de début</strong></td>
                    <td>" . $contrat['date_debut_contrat'] . "</td>
                </tr>
                <tr>
                    <td><strong>Date de fin</strong></td>
                    <td>" . $contrat['date_fin_contrat'] . "</td>
                </tr>
                <tr>
                    <td><strong>Est actif</strong></td>
                    <td>" . ($contrat['est_actif'] ? 'Oui' : 'Non') . "</td>
                </tr>
            </table><br>";
            return true;
        }
    }
    return false;
}


function displayAbsences(String $matricule_emp)
{
    $presences = getPresences();
    if (!$presences) {
        return false;
    }
    foreach ($presences as $presence) {
        if ($presence['matricule_emp'] == $matricule_emp /*&& $presence['statut_presence'] == "absent"*/) {

            echo
            "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
                    <tr>
                        <th style='background-color: #f2f2f2;'>Champ</th>
                        <th style='background-color: #f2f2f2;'>Valeur</th>
                    </tr>
                    <tr>
                        <td><strong>Date</strong></td>
                        <td>" . $presence['date_presence'] . "</td>
                    </tr>
                    <tr>
                        <td><strong>Heure d'arrivée</strong></td>
                        <td>" . $presence['heure_arrive'] . "</td>
                    </tr>
                    <tr>
                        <td><strong>Heure de départ</strong></td>
                        <td>" . $presence['heure_depart'] . "</td>
                    </tr>
                    <tr>
                        <td><strong>Statut</strong></td>
                        <td>" . $presence['statut_presence'] . "</td>
                    </tr>
                </table>";
            return true;
        }
    }
    return false;
}


function displayConges(String $matricule_emp)
{
    $conges = getConges();
    if (!$conges) {
        return false;
    }
    foreach ($conges as $conge) {
        if ($conge['matricule_emp'] == $matricule_emp) {

            echo
            "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
                    <tr>
                        <th style='background-color: #f2f2f2;'>Champ</th>
                        <th style='background-color: #f2f2f2;'>Valeur</th>
                    </tr>
                    <tr>
                        <td><strong>Date de début</strong></td>
                        <td>" . $conge['date_debut_conge'] . "</td>
                    </tr>
                    <tr>
                        <td><strong>Date de fin</strong></td>
                        <td>" . $conge['date_fin_conge'] . "</td>
                    </tr>
                    <tr>
                        <td><strong>Type de congé</strong></td>
                        <td>" . $conge['type_conge'] . "</td>
                    </tr>
                    <tr>
                        <td><strong>Statut du congé</strong></td>
                        <td>" . $conge['statut_conge'] . "</td>
                    </tr>
                    <tr>
                        <td><strong>Nombre de jours restants</strong></td>
                        <td>" . $conge['nb_jour_restant'] . "</td>
                    </tr>
                </table>";
            return true;

        }

    }
    return false ;
}
