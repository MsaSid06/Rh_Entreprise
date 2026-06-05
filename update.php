<?php

require_once "config/database.php";


function updateEmploye(string $matricule_emp, string $nom_emp, string $prenom_emp, string $date_naissance, string $email, string $telephone, string $date_embauche, string $code_poste, string $code_dept)
{

    global $pdo;
    try {
        $sql = "UPDATE Employe SET nom_emp = :nom_emp, prenom_emp = :prenom_emp, date_naiss = :date_naissance, date_embauche = :date_embauche, email = :email, telephone = :telephone, code_poste = :code_poste, code_dept = :code_dept WHERE matricule_emp = :matricule_emp";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule_emp', $matricule_emp);
        $stmt->bindParam(':nom_emp', $nom_emp);
        $stmt->bindParam(':prenom_emp', $prenom_emp);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':date_embauche', $date_embauche);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':code_poste', $code_poste);
        $stmt->bindParam(':code_dept', $code_dept);
        return $stmt->execute();
    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}


function updateContrat(string $matricule_emp, string $type_contrat, string $date_debut_contrat, string $date_fin_contrat, float $salaire_brut, bool $est_actif)
{

    global $pdo;
    try {
        $sql = "UPDATE Contrat SET type_contrat = :type_contrat, date_debut_contrat = :date_debut_contrat, date_fin_contrat = :date_fin_contrat, salaire_brut = :salaire_brut, est_actif = :est_actif WHERE matricule_emp = :matricule_emp";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule_emp', $matricule_emp);
        $stmt->bindParam(':type_contrat', $type_contrat);
        $stmt->bindParam(':date_debut_contrat', $date_debut_contrat);
        $stmt->bindParam(':date_fin_contrat', $date_fin_contrat);
        $stmt->bindParam(':salaire_brut', $salaire_brut);
        $stmt->bindParam(':est_actif', $est_actif, PDO::PARAM_BOOL);
        return $stmt->execute();
    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}


function updateConge(string $matricule_emp, string $type_conge, string $statut_conge, string $date_debut_conge, string $date_fin_conge, int $nb_jour_restant)
{

    global $pdo;
    try {
        $sql = "UPDATE Conge SET type_conge = :type_conge, statut_conge = :statut_conge, date_debut_conge = :date_debut_conge, date_fin_conge = :date_fin_conge, nb_jour_restant = :nb_jour_restant WHERE matricule_emp = :matricule_emp";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule_emp', $matricule_emp);
        $stmt->bindParam(':type_conge', $type_conge);
        $stmt->bindParam(':statut_conge', $statut_conge);
        $stmt->bindParam(':date_debut_conge', $date_debut_conge);
        $stmt->bindParam(':date_fin_conge', $date_fin_conge);
        $stmt->bindParam(':nb_jour_restant', $nb_jour_restant, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}

function updatePresence(string $matricule_emp, string $heure_arrive, string $heure_depart, string $date_presence, string $statut_presence)
{

    global $pdo;
    try {
        $sql = "UPDATE Presence SET heure_arrive = :heure_arrive, heure_depart = :heure_depart, date_presence = :date_presence, statut_presence = :statut_presence WHERE matricule_emp = :matricule_emp";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule_emp', $matricule_emp);
        $stmt->bindParam(':heure_arrive', $heure_arrive);
        $stmt->bindParam(':heure_depart', $heure_depart);
        $stmt->bindParam(':date_presence', $date_presence);
        $stmt->bindParam(':statut_presence', $statut_presence);
        return $stmt->execute();
    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}
