<?php
include "./header.php";
require_once "config/database.php";
// Suivi des contrats :

// 1-) lister les CDD expirant bientôt

function getContratsExpireSoon()
{
    global $pdo;
    try {
        $sql = "SELECT 
                    e.matricule_emp,
                    e.nom_emp,
                    e.prenom_emp,
                    c.type_contrat,
                    c.date_fin_contrat,
                    DATEDIFF(c.date_fin_contrat, CURDATE()) AS jours_restants
                FROM CONTRAT c
                JOIN EMPLOYE e ON c.matricule_emp = e.matricule_emp
                WHERE c.type_contrat     = 'CDD'
                  AND c.est_actif        = TRUE
                  AND c.date_fin_contrat >= CURDATE()
                  AND c.date_fin_contrat <= DATE_ADD(CURDATE(), INTERVAL 15 DAY)
                ORDER BY c.date_fin_contrat ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ["erreur" => $e->getMessage()];
    }
}

// 2-) Afficher les employés sans contrat actif
function getEmployeSansContratActif()
{
    global $pdo;
    try {
        $sql = "SELECT 
                    e.matricule_emp,
                    e.nom_emp,
                    e.prenom_emp,
                    d.nom_dept,
                    p.intitule AS poste
                FROM EMPLOYE e
                JOIN DEPARTEMENT d ON e.code_dept  = d.code_dept
                JOIN POSTE p       ON e.code_poste = p.code_poste
                LEFT JOIN CONTRAT c ON e.matricule_emp = c.matricule_emp
                    AND c.est_actif = TRUE          
                WHERE c.id_contrat IS NULL          
                ORDER BY e.nom_emp ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ["erreur" => $e->getMessage()];
    }
}


// Fonction permettant d'afficher les contrats qui expirent bientot (moins de 15 jours)
function displayContratsExpireSoon()
{
    $contrats = getContratsExpireSoon();
    ?>
<h3>CDD expirant bientôt</h3>

<?php if (empty($contrats)): ?>
<p>Aucun CDD n'expire dans les 15 prochains jours.</p>
<?php else: ?>
<table>
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Expiration</th>
        <th>Jours restants</th>
    </tr>
    <?php foreach ($contrats as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['nom_emp']) ?>
        </td>
        <td><?= htmlspecialchars($c['prenom_emp']) ?>
        </td>
        <td><?= htmlspecialchars($c['date_fin_contrat']) ?>
        </td>
        <td><?= $c['jours_restants'] ?> j</td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif;
}

// Fonction permettant d'afficher les employes sans contrat actifs
function displayEmployesSansContratActif()
{
    $employes = getEmployeSansContratActif();
    ?>
<h3>Employés sans contrat actif</h3>

<?php if (empty($employes)): ?>
<p>Tous les employés ont un contrat actif.</p>
<?php else: ?>
<table>
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Département</th>
        <th>Poste</th>
    </tr>
    <?php foreach ($employes as $e): ?>
    <tr>
        <td><?= htmlspecialchars($e['nom_emp']) ?>
        </td>
        <td><?= htmlspecialchars($e['prenom_emp']) ?>
        </td>
        <td><?= htmlspecialchars($e['nom_dept']) ?>
        </td>
        <td><?= htmlspecialchars($e['poste']) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif;
}
displayEmployesSansContratActif();

displayContratsExpireSoon();
?>