<?php

require_once "config/database.php";

function insertEmploye(string $matricule_emp, string $nom_emp, string $prenom_emp, string $date_naissance, string $email, string $date_embauche, string $code_poste, string $code_dept)
{

    global $pdo;
    try {
        $sql = "INSERT INTO Employe (matricule_emp, nom_emp, prenom_emp, date_naiss, date_embauche, email, code_poste, code_dept) VALUES (:matricule_emp, :nom_emp, :prenom_emp, :date_naiss, :date_embauche, :email, :code_poste, :code_dept)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule_emp', $matricule_emp);
        $stmt->bindParam(':nom_emp', $nom_emp);
        $stmt->bindParam(':prenom_emp', $prenom_emp);
        $stmt->bindParam(':date_naiss', $date_naissance);
        $stmt->bindParam(':date_embauche', $date_embauche);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':code_poste', $code_poste);
        $stmt->bindParam(':code_dept', $code_dept);
        return $stmt->execute();
    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}



function insertPresence(string $matricule_emp, string $heure_arrive, string $heure_depart, string $date_presence, string $statut_presence)
{

    global $pdo;
    try {
        $sql = "INSERT INTO Presence (matricule_emp, heure_arrive, heure_depart, date_presence, statut_presence) VALUES (:matricule_emp, :heure_arrive, :heure_depart, :date_presence, :statut_presence)";
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


function insertContrat(string $matricule_emp, string $type_contrat, string $date_debut_contrat, string $date_fin_contrat, float $salaire_brut, bool $est_actif)
{

    global $pdo;
    try {
        $sql = "INSERT INTO Contrat (matricule_emp, type_contrat, date_debut_contrat, date_fin_contrat, salaire_brut, est_actif) VALUES (:matricule_emp, :type_contrat, :date_debut_contrat, :date_fin_contrat, :salaire_brut, :est_actif)";
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

function insertConge(string $matricule_emp, string $type_conge, string $statut_conge, string $date_debut_conge, string $date_fin_conge, int $nb_jour_restant)
{

    global $pdo;
    try {
        $sql = "INSERT INTO Conge (matricule_emp, type_conge, statut_conge, date_debut_conge, date_fin_conge, nb_jour_restant) VALUES (:matricule_emp, :type_conge, :statut_conge, :date_debut_conge, :date_fin_conge, :nb_jour_restant)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':matricule_emp', $matricule_emp);
        $stmt->bindParam(':type_conge', $type_conge);
        $stmt->bindParam(':statut_conge', $statut_conge);
        $stmt->bindParam(':date_debut_conge', $date_debut_conge);
        $stmt->bindParam(':date_fin_conge', $date_fin_conge);
        $stmt->bindParam(':nb_jour_restant', $nb_jour_restant);
        return $stmt->execute();
    } catch (Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
}