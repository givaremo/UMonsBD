-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 26 déc. 2023 à 10:28
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestionpersonnel`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `usp_affichecalendrier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_affichecalendrier` (IN `idemp` INT, IN `datecal` DATE)  READS SQL DATA select mng.Nom, mng.Prenom, typ.NomTypeEmploye, 
CASE 
when (cong.DateDebutConge <= datecal and cong.DateFinConge >= datecal and cong.estApprouve=1) then 'A'
when (cong.DateDebutConge <= datecal and cong.DateFinConge >= datecal and cong.estApprouve=0 and cong.FkIDEmployeApprouve is NULL) then 'O'
when (cong.DateDebutConge <= datecal and cong.DateFinConge >= datecal and cong.estApprouve=0 and cong.FkIDEmployeApprouve is not NULL) then 'R'
else 'P'
END as presence,
cong.FkIDEmployeApprouve
from employe emp
left join departement dep on(dep.IdDepartement = emp.FKIdDepartement)
left join employe mng on(mng.FKIdEmployeManager = emp.IdEmploye)
left join typeemploye typ on(typ.IdTypeEmploye=mng.FKIdTypeEmploye)
left join conge cong on(mng.IdEmploye=cong.FKIdEmploye)
left join typeconge typcong on(typcong.IdTypeConge=cong.FKIdTypeConge)
where emp.IdEmploye=idemp$$

DROP PROCEDURE IF EXISTS `usp_ajoutAdresse`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutAdresse` (IN `Rue` VARCHAR(200), IN `Numero` VARCHAR(10), IN `Boite` VARCHAR(10), IN `FKIdVille` INT)   insert into Adresse(Rue, NumeroRue, boite, FKIdVille)
		values(Rue, Numero,Boite,FKIdVille)$$

DROP PROCEDURE IF EXISTS `usp_ajoutDepartement`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutDepartement` (IN `NomDepartement` VARCHAR(50))  MODIFIES  DATA insert into departement(Nom) values(NomDepartement)$$

DROP PROCEDURE IF EXISTS `usp_ajoutEmploye`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutEmploye` (IN `nom` VARCHAR(100), IN `prenom` VARCHAR(100), IN `tel` VARCHAR(50), IN `email` VARCHAR(100), IN `motdepasse` VARCHAR(50), IN `iban` VARCHAR(16), IN `niss` VARCHAR(13), IN `fkdepartement` INT, IN `fkadresse` INT, IN `fktypeemploye` INT, IN `fkmanager` INT)   insert into employe(nom,prenom,NumeroTelephone,AdresseMail,password,IBAN,NumeroRegistreNational,FKIDDepartement,FKIDAdresse,FKIdTypeEmploye,FKIdEmployeManager)
values(nom,prenom,tel,email,motdepasse,iban,niss,fkdepartement,fkadresse,fktypeemploye,fkmanager)$$

DROP PROCEDURE IF EXISTS `usp_ajoutNotesdeFrais`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutNotesdeFrais` (IN `DateNoteDeFrais` DATE, IN `DateDemande` DATE, IN `Montant` INT(100) UNSIGNED, IN `Motif` VARCHAR(100), IN `FKIdEmploye` INT(100) UNSIGNED, IN `FKIdEmployeManagerApprouve` INT(100) UNSIGNED, IN `FKIdEmployeFinancierApprouve` INT(100) UNSIGNED)   BEGIN
    INSERT INTO notedefrais (DateNoteDeFrais, DateDemande, Montant, Motif, FKIdEmploye, FKIdEmployeManagerApprouve, FKIdEmployeFinancierApprouve)
    VALUES (DateNoteDeFrais, DateDemande, Montant, Motif, FKIdEmploye, FKIdEmployeManagerApprouve, FKIdEmployeFinancierApprouve);
END$$

DROP PROCEDURE IF EXISTS `usp_ajoutTypeConge`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutTypeConge` (IN `Nom` VARCHAR(100), IN `sansSolde` BOOLEAN)   insert into typeConge(NomTypeConge, estSansSolde) values(Nom, sansSolde)$$

DROP PROCEDURE IF EXISTS `usp_ajoutTypeEmploye`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutTypeEmploye` (IN `nom` VARCHAR(200), IN `chef` BOOLEAN, IN `rh` BOOLEAN, IN `fin` BOOLEAN)   insert into typeEmploye(NomTypeEmploye, estChef, estRH, estDirecteurFinancier)
values(nom,chef,rh,fin)$$

DROP PROCEDURE IF EXISTS `usp_ajoutVille`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutVille` (IN `CP` INT, IN `Nom` VARCHAR(100))   insert into ville(CodePostal, Nomville) values (CP,Nom)$$

DROP PROCEDURE IF EXISTS `usp_connectUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_connectUser` (IN `mail` VARCHAR(100), IN `pwd` VARCHAR(50))   select idemploye, nom, prenom, estChef, estRH, estDirecteurFinancier, nomTypeEmploye
from employe emp
left join typeemploye typemp on(emp.FKIdTypeEmploye = typemp.IdTypeEmploye)
where adressemail=trim(mail)
and password=trim(pwd)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--

DROP TABLE IF EXISTS `adresse`;
CREATE TABLE IF NOT EXISTS `adresse` (
  `IdAdresse` int NOT NULL AUTO_INCREMENT,
  `Rue` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NumeroRue` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'pas int, varchar pour les numéros composés de A, B. Ex : 90A',
  `Boite` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FKIdVille` int NOT NULL,
  PRIMARY KEY (`IdAdresse`),
  KEY `RelationAdresseVille` (`FKIdVille`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adresse`
--

INSERT INTO `adresse` (`IdAdresse`, `Rue`, `NumeroRue`, `Boite`, `FKIdVille`) VALUES
(1, 'Rue de la bataille', '10', '', 1),
(2, 'Rue des Alizés', '25', '', 3),
(3, 'Avenue de l\'Émeraude', '31', 'B', 5),
(4, 'Impasse du Zéphyr', '20', '', 4),
(5, 'Boulevard des Lumières', '1', '', 6),
(6, 'Allée des Charmes', '32', '', 7),
(7, 'Place de la Sérénité', '14', '', 8),
(8, 'Rue du Cèdre', '98', '', 2),
(9, 'Passage des Étoiles', '105', 'A', 8),
(10, 'Avenue du Papillon', '136', '', 5),
(11, 'Chemin du Crépuscule', '18', 'B', 3);

-- --------------------------------------------------------

--
-- Structure de la table `conge`
--

DROP TABLE IF EXISTS `conge`;
CREATE TABLE IF NOT EXISTS `conge` (
  `IdConge` int NOT NULL AUTO_INCREMENT,
  `DateDebutConge` date NOT NULL,
  `DateFinConge` date NOT NULL,
  `estApprouve` tinyint(1) NOT NULL,
  `FKIdEmploye` int NOT NULL,
  `FKIdTypeConge` int NOT NULL,
  `FkIDEmployeApprouve` int DEFAULT NULL,
  PRIMARY KEY (`IdConge`),
  KEY `RelationCongeType` (`FKIdTypeConge`),
  KEY `RelationCongeEmploye` (`FKIdEmploye`),
  KEY `RelationCongeEmployeApprouve` (`FkIDEmployeApprouve`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conge`
--

INSERT INTO `conge` (`IdConge`, `DateDebutConge`, `DateFinConge`, `estApprouve`, `FKIdEmploye`, `FKIdTypeConge`, `FkIDEmployeApprouve`) VALUES
(1, '2023-01-01', '2023-12-31', 1, 2, 1, 1),
(3, '2023-12-26', '2023-12-27', 0, 4, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contrat`
--

DROP TABLE IF EXISTS `contrat`;
CREATE TABLE IF NOT EXISTS `contrat` (
  `IdContrat` int NOT NULL AUTO_INCREMENT,
  `DateDebut` date NOT NULL,
  `DateFin` date NOT NULL,
  `Salaire` float NOT NULL,
  `FKIdEmploye` int NOT NULL,
  PRIMARY KEY (`IdContrat`),
  KEY `RelationContratEmploye` (`FKIdEmploye`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `IdDepartement` int NOT NULL AUTO_INCREMENT,
  `Nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdDepartement`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`IdDepartement`, `Nom`) VALUES
(1, 'Direction Ressources Humaines'),
(2, 'Direction Financière'),
(3, 'Direction Faculté Polytechnique'),
(4, 'Direction Faculté Sciences de Gestion'),
(5, 'Direction ICT'),
(6, 'Direction générale');

-- --------------------------------------------------------

--
-- Structure de la table `employe`
--

DROP TABLE IF EXISTS `employe`;
CREATE TABLE IF NOT EXISTS `employe` (
  `IdEmploye` int NOT NULL AUTO_INCREMENT,
  `Nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NumeroTelephone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `AdresseMail` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'doit contenir "@" + "."',
  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IBAN` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'BE + 14 chiffres',
  `NumeroRegistreNational` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '6chiffres + "-" + 3 chiffres + "-" + 2 chiffres',
  `FKIdDepartement` int NOT NULL,
  `FKIdAdresse` int NOT NULL,
  `FKIdTypeEmploye` int NOT NULL,
  `FKIdEmployeManager` int DEFAULT NULL,
  PRIMARY KEY (`IdEmploye`),
  KEY `RelationEmployeDepartement` (`FKIdDepartement`),
  KEY `RelationEmployeAdresse` (`FKIdAdresse`),
  KEY `RelationEmployeTypeEmploye` (`FKIdTypeEmploye`),
  KEY `RelationEmployeManager` (`FKIdEmployeManager`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `employe`
--

INSERT INTO `employe` (`IdEmploye`, `Nom`, `Prenom`, `NumeroTelephone`, `AdresseMail`, `password`, `IBAN`, `NumeroRegistreNational`, `FKIdDepartement`, `FKIdAdresse`, `FKIdTypeEmploye`, `FKIdEmployeManager`) VALUES
(1, 'Dupont', 'Michel', '0495/78.32.56', 'Michel.Dupont@umons.ac.be', 'toto1234', 'BE56123456789012', '521032-123-56', 6, 4, 6, NULL),
(2, 'Dubois', 'Micheline', '0492/23.14.69', 'Micheline.Dubois@umons.ac.be', 'toto5678', 'BE56369874521036', '508030-987-23', 1, 1, 2, 1),
(3, 'Lebeau', 'Marc', '0475/69.23.12', 'Marc.Lebeau@umons.ac.be', 'abc123', 'BE23569874526987', '653256-236-52', 1, 6, 1, 2),
(4, 'Legrand', 'Papy', '0492/22.01.63', 'Papy.Legrand@umons.ac.be', 'azerty99', 'BE32458963214568', '745632-122-33', 2, 7, 5, 1),
(5, 'Lefevre', 'Jean', '0475/69.60.24', 'Lefevre.Jean@umons.ac.be', 'hHet0iKR', 'BE65752969841892', '908084-408-85', 1, 6, 1, 2),
(6, 'Laurent', 'Pierre', '0471/77.19.17', 'Laurent.Pierre@umons.ac.be', 'xULKShVY', 'BE89547603462899', '350116-130-85', 5, 6, 4, 1),
(7, 'Martin', 'Isabelle', '0478/75.40.17', 'Martin.Isabelle@umons.ac.be', '2jVqsAtL', 'BE34623475511483', '683274-931-46', 3, 5, 4, 1),
(8, 'Dupont', 'Nicolas', '0476/44.62.75', 'Dupont.Nicolas@umons.ac.be', 'KTcbuQlH', 'BE72404584550074', '644334-550-33', 1, 2, 3, 2),
(9, 'Laurent', 'Emilie', '0472/88.10.24', 'Laurent.Emilie@umons.ac.be', 'Xt4cFplY', 'BE68695250856528', '809137-968-32', 1, 1, 3, 2),
(10, 'Dupont', 'Pierre', '0474/50.36.57', 'Dupont.Pierre@umons.ac.be', 'YrvepZ9U', 'BE38513885778737', '856104-195-91', 3, 3, 3, 6),
(11, 'Simon', 'Marie', '0472/69.46.14', 'Simon.Marie@umons.ac.be', '0RTgXWNU', 'BE25417586795491', '199968-901-74', 2, 1, 3, 4),
(12, 'Laurent', 'Sophie', '0473/71.21.43', 'Laurent.Sophie@umons.ac.be', '8hDYaElp', 'BE40370817410138', '139426-899-38', 4, 4, 4, 1),
(13, 'Roux', 'Jean', '0477/53.71.19', 'Roux.Jean@umons.ac.be', 'LBKw7ZpW', 'BE43497160491169', '503035-686-38', 1, 7, 3, 2),
(14, 'Dupont', 'Charlotte', '0478/37.72.92', 'Dupont.Charlotte@umons.ac.be', 'Sg0JHNaX', 'BE17286179548286', '414750-553-98', 5, 8, 3, 6),
(15, 'Martin', 'Marie', '0474/80.73.82', 'Martin.Marie@umons.ac.be', 'U3urgvlk', 'BE85002582842948', '866084-617-55', 5, 9, 3, 6),
(16, 'Dupont', 'Marie', '0474/68.77.55', 'Dupont.Marie@umons.ac.be', '4nr7mGfJ', 'BE71922875158636', '209180-828-59', 3, 6, 3, 7),
(17, 'Leroy', 'Charlotte', '0473/38.28.46', 'Leroy.Charlotte@umons.ac.be', '6A4rp0kD', 'BE76038585956236', '981671-846-33', 2, 3, 3, 4),
(18, 'Roux', 'Marie', '0472/58.57.51', 'Roux.Marie@umons.ac.be', 'moJ8MPyK', 'BE72359187417400', '716656-498-53', 3, 2, 3, 7),
(19, 'Laurent', 'Charlotte', '0471/56.34.70', 'Laurent.Charlotte@umons.ac.be', 'b7CWVvf1', 'BE67277359028123', '159035-355-47', 1, 1, 3, 2),
(20, 'Simon', 'Marie', '0472/86.62.59', 'Simon.Marie@umons.ac.be', 'ewqDAxj6', 'BE26678737445705', '106756-163-71', 6, 2, 4, 1),
(21, 'Dupont', 'Luc', '0473/82.70.58', 'Dupont.Luc@umons.ac.be', 'gakmPtlZ', 'BE16536708302112', '450605-622-52', 5, 3, 3, 7),
(22, 'Simon', 'Nicolas', '0478/80.22.62', 'Simon.Nicolas@umons.ac.be', 'fMcdCFUK', 'BE73044680601981', '874242-628-65', 3, 6, 3, 7),
(23, 'Simon', 'Pierre', '0478/31.39.91', 'Simon.Pierre@umons.ac.be', 'rgCj2V0i', 'BE87743904989934', '215565-206-73', 2, 5, 3, 4),
(24, 'Laurent', 'Nicolas', '0471/19.18.14', 'Laurent.Nicolas@umons.ac.be', '5KCJNtOh', 'BE79931301006533', '498985-102-77', 4, 4, 4, 1),
(25, 'Martin', 'Antoine', '0473/22.12.17', 'Martin.Antoine@umons.ac.be', 'HEjbnMJ3', 'BE80429916317127', '749226-152-75', 5, 1, 3, 6),
(26, 'Dubois', 'Isabelle', '0471/55.63.66', 'Dubois.Isabelle@umons.ac.be', '0FSWZqfl', 'BE69607462163233', '854541-959-57', 2, 2, 3, 4),
(27, 'Simon', 'Isabelle', '0473/18.63.65', 'Simon.Isabelle@umons.ac.be', 'x07Oq2hH', 'BE82625482592123', '257767-134-30', 6, 3, 3, 1),
(28, 'Lefort', 'Isabelle', '0477/54.87.27', 'Lefort.Isabelle@umons.ac.be', 'bZj6pLVs', 'BE77141417110015', '248345-722-70', 3, 6, 3, 7),
(29, 'Laurent', 'Marie', '0477/64.97.21', 'Laurent.Marie@umons.ac.be', 'cL67Y0rN', 'BE85983205285884', '951458-249-20', 1, 5, 3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `fichedepaie`
--

DROP TABLE IF EXISTS `fichedepaie`;
CREATE TABLE IF NOT EXISTS `fichedepaie` (
  `IdFiche` int NOT NULL AUTO_INCREMENT,
  `Salaire` float NOT NULL,
  `PeriodeDebutSalaire` date NOT NULL,
  `PeriodeFinSalaire` date NOT NULL,
  `NbJoursRemuneres` int NOT NULL,
  `NbJoursSansSolde` int NOT NULL,
  `FKIdEmploye` int NOT NULL,
  PRIMARY KEY (`IdFiche`),
  KEY `RelationFichePaieEmploye` (`FKIdEmploye`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notedefrais`
--

DROP TABLE IF EXISTS `notedefrais`;
CREATE TABLE IF NOT EXISTS `notedefrais` (
  `IdNoteDeFrais` int NOT NULL AUTO_INCREMENT,
  `DateNoteDeFrais` date NOT NULL,
  `DateDemande` date NOT NULL,
  `Montant` float NOT NULL,
  `Motif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FKIdEmploye` int NOT NULL,
  `FKIdEmployeManagerApprouve` int DEFAULT NULL,
  `FKIdEmployeFinancierApprouve` int DEFAULT NULL,
  PRIMARY KEY (`IdNoteDeFrais`),
  KEY `RelationNoteFraisEmploye` (`FKIdEmploye`),
  KEY `RelationNoteFraisEmployeManager` (`FKIdEmployeManagerApprouve`),
  KEY `RelationNoteFraisEmployeFinancier` (`FKIdEmployeFinancierApprouve`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notedefrais`
--

INSERT INTO `notedefrais` (`IdNoteDeFrais`, `DateNoteDeFrais`, `DateDemande`, `Montant`, `Motif`, `FKIdEmploye`, `FKIdEmployeManagerApprouve`, `FKIdEmployeFinancierApprouve`) VALUES
(1, '2023-12-26', '2023-12-26', 100, 'cadeau de noel', 1, NULL, NULL),
(2, '2023-12-28', '2023-12-28', 250, 'cadeaux', 1, NULL, NULL),
(3, '2023-12-27', '2023-12-27', 12, 'pppp', 1, NULL, NULL),
(4, '2023-12-14', '2023-12-14', 123, 'aaa', 1, NULL, NULL),
(5, '2023-12-14', '2023-12-14', 123, 'aaa', 1, NULL, NULL),
(6, '2023-12-07', '2023-12-07', 250, '123', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `typeconge`
--

DROP TABLE IF EXISTS `typeconge`;
CREATE TABLE IF NOT EXISTS `typeconge` (
  `IdTypeConge` int NOT NULL AUTO_INCREMENT,
  `NomTypeConge` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estSansSolde` tinyint(1) NOT NULL,
  PRIMARY KEY (`IdTypeConge`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `typeconge`
--

INSERT INTO `typeconge` (`IdTypeConge`, `NomTypeConge`, `estSansSolde`) VALUES
(1, 'Congé légal', 0),
(2, 'Congé récupération heure supplémentaires', 0),
(3, 'Congé force majeure', 0),
(4, 'Congé maternité', 0),
(5, 'Congé éducation', 0),
(6, 'Congé sans solde', 1);

-- --------------------------------------------------------

--
-- Structure de la table `typeemploye`
--

DROP TABLE IF EXISTS `typeemploye`;
CREATE TABLE IF NOT EXISTS `typeemploye` (
  `IdTypeEmploye` int NOT NULL AUTO_INCREMENT,
  `NomTypeEmploye` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estChef` tinyint(1) NOT NULL,
  `estRH` tinyint(1) NOT NULL,
  `estDirecteurFinancier` tinyint(1) NOT NULL,
  PRIMARY KEY (`IdTypeEmploye`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `typeemploye`
--

INSERT INTO `typeemploye` (`IdTypeEmploye`, `NomTypeEmploye`, `estChef`, `estRH`, `estDirecteurFinancier`) VALUES
(1, 'Employé RH', 0, 1, 0),
(2, 'Manager RH', 1, 1, 0),
(3, 'Employé standard', 0, 0, 0),
(4, 'Manager standard', 1, 0, 0),
(5, 'Directeur Financier', 1, 0, 1),
(6, 'Doyen', 1, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

DROP TABLE IF EXISTS `ville`;
CREATE TABLE IF NOT EXISTS `ville` (
  `IdVille` int NOT NULL AUTO_INCREMENT,
  `CodePostal` int NOT NULL,
  `NomVille` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdVille`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ville`
--

INSERT INTO `ville` (`IdVille`, `CodePostal`, `NomVille`) VALUES
(1, 7332, 'Sirault'),
(2, 7332, 'Neufmaison'),
(3, 7000, 'Mons'),
(4, 6000, 'Charleroi'),
(5, 1000, 'Bruxelles'),
(6, 5000, 'Namur'),
(7, 4000, 'Liège'),
(8, 7500, 'Tournai');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD CONSTRAINT `RelationAdresseVille` FOREIGN KEY (`FKIdVille`) REFERENCES `ville` (`IdVille`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `conge`
--
ALTER TABLE `conge`
  ADD CONSTRAINT `RelationCongeEmploye` FOREIGN KEY (`FKIdEmploye`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelationCongeEmployeApprouve` FOREIGN KEY (`FkIDEmployeApprouve`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelationCongeType` FOREIGN KEY (`FKIdTypeConge`) REFERENCES `typeconge` (`IdTypeConge`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `contrat`
--
ALTER TABLE `contrat`
  ADD CONSTRAINT `RelationContratEmploye` FOREIGN KEY (`FKIdEmploye`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `employe`
--
ALTER TABLE `employe`
  ADD CONSTRAINT `RelationEmployeAdresse` FOREIGN KEY (`FKIdAdresse`) REFERENCES `adresse` (`IdAdresse`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelationEmployeDepartement` FOREIGN KEY (`FKIdDepartement`) REFERENCES `departement` (`IdDepartement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelationEmployeManager` FOREIGN KEY (`FKIdEmployeManager`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelationEmployeTypeEmploye` FOREIGN KEY (`FKIdTypeEmploye`) REFERENCES `typeemploye` (`IdTypeEmploye`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `fichedepaie`
--
ALTER TABLE `fichedepaie`
  ADD CONSTRAINT `RelationFichePaieEmploye` FOREIGN KEY (`FKIdEmploye`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `notedefrais`
--
ALTER TABLE `notedefrais`
  ADD CONSTRAINT `RelationNoteFraisEmploye` FOREIGN KEY (`FKIdEmploye`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelationNoteFraisEmployeFinancier` FOREIGN KEY (`FKIdEmployeFinancierApprouve`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelationNoteFraisEmployeManager` FOREIGN KEY (`FKIdEmployeManagerApprouve`) REFERENCES `employe` (`IdEmploye`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
