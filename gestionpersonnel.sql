-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 22 déc. 2023 à 14:38
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
DROP PROCEDURE IF EXISTS `usp_ajoutAdresse`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutAdresse` (IN `Rue` VARCHAR(200), IN `Numero` VARCHAR(10), IN `Boite` VARCHAR(10), IN `FKIdVille` INT)   insert into Adresse(Rue, NumeroRue, boite, FKIdVille)
		values(Rue, Numero,Boite,FKIdVille)$$

DROP PROCEDURE IF EXISTS `usp_ajoutDepartement`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutDepartement` (IN `NomDepartement` VARCHAR(50))  MODIFIES  DATA insert into departement(Nom) values(NomDepartement)$$

DROP PROCEDURE IF EXISTS `usp_ajoutEmploye`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_ajoutEmploye` (IN `nom` VARCHAR(100), IN `prenom` VARCHAR(100), IN `tel` VARCHAR(50), IN `email` VARCHAR(100), IN `motdepasse` VARCHAR(50), IN `iban` VARCHAR(16), IN `niss` VARCHAR(13), IN `fkdepartement` INT, IN `fkadresse` INT, IN `fktypeemploye` INT, IN `fkmanager` INT)   insert into employe(nom,prenom,NumeroTelephone,AdresseMail,password,IBAN,NumeroRegistreNational,FKIDDepartement,FKIDAdresse,FKIdTypeEmploye,FKIdEmployeManager)
values(nom,prenom,tel,email,motdepasse,iban,niss,fkdepartement,fkadresse,fktypeemploye,fkmanager)$$

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `employe`
--

INSERT INTO `employe` (`IdEmploye`, `Nom`, `Prenom`, `NumeroTelephone`, `AdresseMail`, `password`, `IBAN`, `NumeroRegistreNational`, `FKIdDepartement`, `FKIdAdresse`, `FKIdTypeEmploye`, `FKIdEmployeManager`) VALUES
(1, 'Dupont', 'Michel', '0495/78.32.56', 'Michel.Dupont@umons.ac.be', 'toto1234', 'BE56123456789012', '521032-123-56', 6, 4, 6, NULL),
(2, 'Dubois', 'Micheline', '0492/23.14.69', 'Micheline.Dubois@umons.ac.be', 'toto5678', 'BE56369874521036', '508030-987-23', 1, 1, 2, 1),
(3, 'Lebeau', 'Marc', '0475/69.23.12', 'Marc.Lebeau@umons.ac.be', 'abc123', 'BE23569874526987', '653256-236-52', 1, 6, 1, 2),
(4, 'Legrand', 'Papy', '0492/22.01.63', 'Papy.Legrand@umons.ac.be', 'azerty99', 'BE32458963214568', '745632-122-33', 2, 7, 5, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
