DROP DATABASE IF EXISTS rh_entreprise;
create database rh_entreprise;
use rh_entreprise;

-- BULLETIN (id_bulletin,#maticule_emp,mois, annee, salaire_brut, nbr_jours_travaille, nbr_abs_injustifie, retenues, salaire_net)

-- DEPARTEMENT(code_dept, nom_dept, #matricule_emp, budget_annuel)

-- POSTE(code_poste, intitule, salaire_base, niv_resp)

-- PRESENCE(id_pres, #maticule_emp, type_pres, heure_arrive, heure_depart, statut_presence)

-- EMPLOYE(matricule_emp, nom_emp, prenom_emp, date_naiss, date_embauche, email, telephone, #contrat_actif, #code_dept, #code_poste)

-- CONGE(id_conge, #maticule_emp, type_conge, date_debut_conge, date_fin_conge, statut_conge,nb_jour_restant)

-- CONTRAT(id_contrat, #maticule_emp, type_contrat, date_debut_contrat, date_fin_contrat, salaire_brut, est_actif)

-- Modif appporter : bulletin (cle primaire ), salaire_net retire car etant une donne calcule), ajout contrat_actif dans employe pour faire la jointure avec contrat et trouver le contrat actif de l'employé, retenues retire de bulletin car c'est une donnée calculée a partir du salaire brut et du nombre d'absences injustifiées, 
create table POSTE(
    code_poste char(5) not null,
    intitule varchar(50) not null,
    salaire_base decimal(15,2) not null check (salaire_base >= 0),
    niv_resp varchar(20) not null,

    constraint pk_poste primary key (code_poste)
);


-- a ce niveau on a pas encore les fk car on risque d'avoir des dependance circulaire entre les tables, on les ajoutera apres la creation de toutes les tables

create table DEPARTEMENT(
    code_dept char(5) not null,
    nom_dept varchar(50) not null,
    matricule_emp char(5) default null,
    budget_annuel decimal(15,2) not null check (budget_annuel >= 0),

    constraint pk_departement primary key (code_dept)
);


create table EMPLOYE(
    matricule_emp char(5) not null,
    nom_emp varchar(50) not null,
    prenom_emp varchar(50)not null,
    date_naiss date not null,
    date_embauche date not null,
    email varchar(100) not null unique check (email like '%@%.%'),
    telephone varchar(20) not null unique,
    code_dept char(5) not null,
    code_poste char(5) not null,
    
    constraint pk_employe primary key (matricule_emp),
    constraint fk_employe_poste_code_poste foreign key (code_poste) references POSTE(code_poste)
);



create table BULLETIN(
    id_bulletin int auto_increment,
    matricule_emp char(5) not null,
    mois int check (mois >= 1 and mois <= 12),
    annee int check (annee >= 1900 and annee <= 2100),
    salaire_brut decimal(15,2) not null check (salaire_brut >= 0),
    nbr_jours_travaille int check (nbr_jours_travaille >= 0),
    nbr_abs_injustifie int default 0 check (nbr_abs_injustifie >= 0),

    constraint pk_bulletin primary key (id_bulletin,mois,annee, matricule_emp),
    constraint fk_bulletin_employe_matricule_emp foreign key (matricule_emp) references EMPLOYE(matricule_emp)
);

/*j'ai pas mis current timestamp car on peut pas savoir qd la presence est enregistrée */
create table PRESENCE(
    id_pres int auto_increment,
    matricule_emp char(5) not null,
    heure_arrive time not null,
    heure_depart time not null,
    date_presence date not null,
    statut_presence varchar(10) check (statut_presence in ('present', 'absent','Conge')),

    constraint pk_presence primary key (id_pres),
    constraint fk_presence_employe_matricule_emp foreign key (matricule_emp) references EMPLOYE(matricule_emp)
);

create table CONGE(
    id_conge int auto_increment ,
    matricule_emp char(5) not null,
    type_conge varchar(10) not null check (type_conge in ('annuel', 'maladie', 'maternite')),
    statut_conge varchar(10) not null check (statut_conge in ('approuve', 'refuse', 'demande')),
    date_debut_conge date not null,
    date_fin_conge date not null,
    nb_jour_restant int check (nb_jour_restant >= 0),

    constraint pk_conge primary key (id_conge),
    constraint fk_conge_employe_matricule_emp foreign key (matricule_emp) references EMPLOYE(matricule_emp)
);

create table CONTRAT(
    id_contrat int auto_increment,
    matricule_emp char(5) not null,
    type_contrat varchar(10) not null check (type_contrat in ('CDI', 'CDD', 'Stage')),
    date_debut_contrat date not null,
    date_fin_contrat date not null,
    salaire_brut decimal(15,2) not null check (salaire_brut >= 0),
    est_actif boolean not null default true,

    constraint pk_contrat primary key (id_contrat),
    constraint fk_contrat_employe_matricule_emp foreign key (matricule_emp) references EMPLOYE(matricule_emp)
);

-- eviter d'inserer un contrat actif pour un employé qui en a déjà un autre actif
DELIMITER //

CREATE TRIGGER verif_contrat_actif_insert
BEFORE INSERT ON CONTRAT
FOR EACH ROW
BEGIN
    DECLARE nb_contrats INT;

    IF NEW.est_actif = TRUE THEN

        SELECT COUNT(*)
        INTO nb_contrats
        FROM CONTRAT
        WHERE matricule_emp = NEW.matricule_emp
        AND est_actif = TRUE;

        IF nb_contrats > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Un contrat actif existe déjà pour cet employé.';
        END IF;

    END IF;
END//

DELIMITER ;

-- eviter de mettre a jour un contrat pour le rendre actif si un autre contrat actif existe deja pour le meme employe

DELIMITER //

CREATE TRIGGER verif_contrat_actif_update
BEFORE UPDATE ON CONTRAT
FOR EACH ROW
BEGIN
    DECLARE nb_contrats INT; -- variable pour stocker le nombre de contrats actifs de l'employé

    IF NEW.est_actif = TRUE THEN

        SELECT COUNT(*)
        INTO nb_contrats
        FROM CONTRAT
        WHERE matricule_emp = NEW.matricule_emp
        AND est_actif = TRUE
        AND id_contrat <> NEW.id_contrat; -- on exclut le contrat en cours de mise à jour

        IF nb_contrats > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Un contrat actif existe déjà pour cet employé.';
        END IF;

    END IF;
END//

DELIMITER ;


-- ajout des contraintes de clés étrangères après la création de toutes les tables pour éviter les problèmes de dépendances circulaires

alter table DEPARTEMENT
    add constraint fk_departement_employe_matricule_emp foreign key (matricule_emp) references EMPLOYE(matricule_emp);

alter table EMPLOYE
    add constraint fk_employe_departement_code_dept foreign key (code_dept) references DEPARTEMENT(code_dept);      



-- Insérer au minimum : 4 départements, 8 postes, 20 employés, 20 contrats, 30 présences, 10 congés, 20 bulletins.


INSERT INTO POSTE (code_poste, intitule, salaire_base, niv_resp) VALUES
('P001','Manager',5000,'Haut'),
('P002','Développeur',3000,'Moyen'),
('P003','Analyste',3400,'Moyen'),
('P004','Assistant RH',2400,'Bas'),
('P005','Commercial',2800,'Moyen'),
('P006','Comptable',3200,'Moyen'),
('P007','Stagiaire IT',1200,'Bas'),
('P008','Chef de projet',4500,'Haut'),
('P009','DevOps',4100,'Haut'),
('P010','Data Analyst',3600,'Moyen'),
('P011','UI Designer',3100,'Moyen'),
('P012','Support IT',2000,'Bas'),
('P013','Auditeur',3800,'Moyen'),
('P014','Marketing Digital',2900,'Moyen'),
('P015','RH Senior',4000,'Haut'),
('P016','Sécurité SI',4200,'Haut'),
('P017','Architecte Logiciel',4800,'Haut'),
('P018','Technicien IT',2200,'Bas'),
('P019','Assistant Comptable',2300,'Bas'),
('P020','Community Manager',2700,'Moyen');


INSERT INTO DEPARTEMENT (code_dept, nom_dept, budget_annuel) VALUES
('D001','Ressources Humaines',600000),
('D002','Informatique',1200000),
('D003','Marketing',800000),
('D004','Finance',700000);


INSERT INTO EMPLOYE (matricule_emp, nom_emp, prenom_emp, date_naiss, date_embauche, email, telephone, code_dept, code_poste) VALUES
('E001','Dupont','Jean','1985-06-15','2010-01-10','jean.dupont@entreprise.com','770000001','D001','P001'),
('E002','Martin','Sophie','1990-09-20','2022-03-25','sophie.martin@entreprise.com','770000002','D002','P002'),
('E003','Durand','Pierre','1988-12-05','2019-07-18','pierre.durand@entreprise.com','770000003','D003','P003'),
('E004','Lefevre','Marie','1992-03-10','2023-11-01','marie.lefevre@entreprise.com','770000004','D004','P004'),
('E005','Moreau','Luc','1987-08-25','2015-05-20','luc.moreau@entreprise.com','770000005','D001','P005'),
('E006','Girard','Emma','1991-11-30','2021-09-15','emma.girard@entreprise.com','770000006','D002','P006'),
('E007','Roux','Paul','1989-04-18','2020-02-28','paul.roux@entreprise.com','770000007','D003','P007'),
('E008','Blanc','Alice','1993-07-22','2024-06-10','alice.blanc@entreprise.com','770000008','D004','P008'),
('E009','Fontaine','Lucie','1986-10-05','2018-04-01','lucie.fontaine@entreprise.com','770000009','D001','P009'),
('E010','Chevalier','Maxime','1990-01-15','2020-08-20','maxime.chevalier@entreprise.com','770000010','D002','P010'),
('E011','Renaud','Camille','1988-05-30','2022-12-01','camille.renaud@entreprise.com','770000011','D003','P011'),
('E012','Garnier','Nicolas','1991-09-10','2021-01-15','nicolas.garnier@entreprise.com','770000012','D004','P012'),
('E013','Chevrier','Julie','1987-02-20','2019-10-01','julie.chevrier@entreprise.com','770000013','D001','P013'),
('E014','Lemoine','Antoine','1989-11-05','2023-06-01','antoine.lemoine@entreprise.com','770000014','D002','P014'),
('E015','Faure','Isabelle','1992-04-15','2024-02-01','isabelle.faure@entreprise.com','770000015','D003','P015'),
('E016','Barbier','Sophie','1986-07-30','2016-09-01','sophie.barbier@entreprise.com','770000016','D004','P016'),
('E017','Leclerc','David','1988-12-10','2020-03-01','david.leclerc@entreprise.com','770000017','D001','P017'),
('E018','Perrin','Caroline','1990-05-25','2023-11-01','caroline.perrin@entreprise.com','770000018','D002','P018'),
('E019','Marchand','Julien','1987-09-15','2021-04-01','julien.marchand@entreprise.com','770000019','D003','P019'),
('E020','Gautier','Amélie','1991-02-28','2022-08-01','amelie.gautier@entreprise.com','770000020','D004','P020');


INSERT INTO CONTRAT (matricule_emp, type_contrat, date_debut_contrat, date_fin_contrat, salaire_brut, est_actif) VALUES
('E001','CDI','2010-01-10','2035-01-10',5000,TRUE),
('E002','CDD','2024-01-01','2026-01-30',3000,TRUE),
('E003','CDI','2019-07-18','2029-07-18',3400,TRUE),
('E004','Stage','2024-09-01','2025-03-01',2400,TRUE),
('E005','CDI','2015-05-20','2030-05-20',2800,TRUE),
('E006','CDD','2023-09-15','2026-09-15',3200,TRUE),
('E007','Stage','2024-01-10','2024-07-10',1200,FALSE),
('E008','CDI','2024-06-10','2025-01-10',4500,TRUE),
('E009','CDI','2018-04-01','2030-04-01',4100,TRUE),
('E010','CDD','2022-08-20','2024-12-20',3600,FALSE),
('E011','CDI','2022-12-01','2032-12-01',3100,TRUE),
('E012','CDI','2021-01-15','2036-01-15',4200,TRUE),
('E013','Stage','2023-10-01','2024-03-01',3800,FALSE),
('E014','CDD','2023-06-01','2026-06-01',2900,TRUE),
('E015','CDI','2024-02-01','2024-12-31',4000,TRUE),
('E016','CDI','2016-09-01','2036-09-01',4200,TRUE),
('E017','CDD','2020-03-01','2025-03-01',2200,TRUE),
('E018','CDI','2023-11-01','2024-12-15',4800,TRUE),
('E019','CDI','2021-04-01','2031-04-01',2700,TRUE),
('E020','Stage','2022-08-01','2023-01-31',2300,FALSE);


INSERT INTO PRESENCE (matricule_emp, heure_arrive, heure_depart, date_presence, statut_presence) VALUES
('E001','08:00','17:00','2026-06-01','present'),
('E002','09:00','18:00','2026-06-01','absent'),
('E003','08:30','17:30','2026-06-01','present'),
('E004','00:00','00:00','2026-06-01','Conge'),
('E005','08:15','17:15','2026-06-01','present'),
('E006','09:30','18:30','2026-06-01','absent'),
('E007','08:45','17:45','2026-06-01','present'),
('E008','08:00','17:00','2026-06-01','present'),
('E009','09:15','18:15','2026-06-01','present'),
('E010','08:30','17:30','2026-06-01','absent');


INSERT INTO CONGE (matricule_emp, type_conge, date_debut_conge, date_fin_conge, statut_conge, nb_jour_restant) VALUES
('E001','annuel','2026-07-01','2026-07-15','approuve',12),
('E002','maladie','2026-06-10','2026-06-20','approuve',5),
('E003','annuel','2026-08-01','2026-08-10','demande',30),
('E004','annuel','2026-09-01','2026-09-10','refuse',0),
('E005','annuel','2026-07-15','2026-07-30','approuve',18),
('E006','maladie','2026-06-15','2026-06-25','approuve',7),
('E007','annuel','2026-07-01','2026-07-20','approuve',25),
('E008','annuel','2026-06-20','2026-06-30','approuve',30),
('E009','annuel','2026-11-01','2026-11-15','demande',120),
('E010','annuel','2026-08-01','2026-08-15','refuse',0),
('E011','annuel','2026-07-10','2026-07-25','approuve',22),
('E012','annuel','2026-06-01','2026-06-15','approuve',15),
('E013','annuel','2026-10-01','2026-10-15','demande',28),
('E014','annuel','2026-06-05','2026-06-20','approuve',10),
('E015','annuel','2026-07-01','2026-07-10','approuve',30),
('E016','annuel','2026-08-01','2026-08-20','approuve',45),
('E017','annuel','2026-09-01','2026-09-20','approuve',60),
('E018','annuel','2026-06-15','2026-06-25','approuve',8),
('E019','annuel','2026-07-01','2026-07-15','approuve',14),
('E020','annuel','2026-05-01','2026-05-15','refuse',0);


INSERT INTO BULLETIN (matricule_emp, mois, annee, salaire_brut, nbr_jours_travaille, nbr_abs_injustifie) VALUES
('E001',1,2026,5000,22,0),
('E001',2,2026,5000,21,1),
('E001',3,2026,5000,22,0),
('E002',1,2026,3000,20,2),
('E002',2,2026,3000,18,3),
('E002',3,2026,3000,22,0),
('E003',1,2026,3400,22,0),
('E003',2,2026,3400,22,0),
('E003',3,2026,3400,20,2),
('E004',1,2026,2400,15,5),
('E004',2,2026,2400,10,10),
('E005',1,2026,2800,22,0),
('E005',2,2026,2800,22,0),
('E005',3,2026,2800,21,1),
('E006',1,2026,3200,19,2),
('E006',2,2026,3200,20,1),
('E007',1,2026,1200,20,1),
('E007',2,2026,1200,18,2),
('E008',6,2026,4500,22,0),
('E008',7,2026,4500,22,0),
('E009',1,2026,4100,22,0),
('E009',2,2026,4100,22,0),
('E009',3,2026,4100,22,0),
('E010',1,2026,3600,18,4),
('E010',2,2026,3600,20,2),
('E011',1,2026,3100,22,0),
('E011',2,2026,3100,21,1),
('E012',1,2026,4200,22,0),
('E012',2,2026,4200,22,0),
('E013',1,2026,3800,16,4),
('E013',2,2026,3800,18,2),
('E014',1,2026,2900,21,1),
('E014',2,2026,2900,22,0),
('E015',1,2026,4000,22,0),
('E015',2,2026,4000,22,0),
('E016',1,2026,4200,22,0),
('E016',2,2026,4200,22,0),
('E017',1,2026,2200,20,2),
('E017',2,2026,2200,19,3),
('E018',1,2026,4800,22,0),
('E018',2,2026,4800,22,0),
('E019',1,2026,2700,22,0),
('E019',2,2026,2700,21,1),
('E020',1,2026,2300,10,10);

update DEPARTEMENT set matricule_emp = 'E001' where code_dept = 'D001';
update DEPARTEMENT set matricule_emp = 'E002' where code_dept = 'D002';
update DEPARTEMENT set matricule_emp = 'E003' where code_dept = 'D003';
update DEPARTEMENT set matricule_emp = 'E004' where code_dept = 'D004';

select * from DEPARTEMENT;
select * from POSTE;
select * from EMPLOYE;
select * from CONTRAT;
select * from PRESENCE;
select * from CONGE;select * from BULLETIN;

