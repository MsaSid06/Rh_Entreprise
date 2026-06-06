<?php

include "./header.php";
require_once "config/database.php";
//Tableau de bord RH :

// 1-) Masse salariale du mois


// 1-) Masse salariale du mois courant
function getMasseSalarialeMoisCourant()
{
    global $pdo;
    try {
        $sql = "SELECT SUM(salaire_brut) AS masse_salariale_totale
                FROM BULLETIN
                WHERE mois  = MONTH(CURDATE())
                  AND annee = YEAR(CURDATE())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ["erreur" => $e->getMessage()];
    }
}

// 2-) Taux d'absentéisme du mois courant
function getTauxAbsenteisme()
{
    global $pdo;
    try {
        $sql = "SELECT 
                    ROUND(
                        SUM(nbr_abs_injustifie) * 100.0
                        / NULLIF(SUM(nbr_jours_travaille + nbr_abs_injustifie), 0),2) AS taux_absenteisme_global
                FROM BULLETIN
                WHERE mois  = MONTH(CURDATE())
                  AND annee = YEAR(CURDATE())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ["erreur" => $e->getMessage()];
    }
}

// 3-) Congés en attente d'approbation
function getCongeEnAttenteApprobation()
{
    global $pdo;
    try {
        $sql = "SELECT 
                    COUNT(*) AS nb_conges_en_attente,
                    c.matricule_emp,
                    e.nom_emp,
                    e.prenom_emp,
                    c.type_conge,
                    c.date_debut_conge,
                    c.date_fin_conge
                FROM CONGE c
                JOIN EMPLOYE e ON c.matricule_emp = e.matricule_emp
                WHERE c.statut_conge = 'demande'
                GROUP BY c.matricule_emp, e.nom_emp, e.prenom_emp,
                         c.type_conge, c.date_debut_conge, c.date_fin_conge
                ORDER BY c.date_debut_conge ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ["erreur" => $e->getMessage()];
    }
}

$masse    = getMasseSalarialeMoisCourant();
$taux     = getTauxAbsenteisme();
$conges   = getCongeEnAttenteApprobation();

echo $masse['masse_salariale_totale'] . " FCFA <br/>";
echo $taux['taux_absenteisme_global'] . " % <br/>";
echo count($conges) . " congé(s) en attente";
