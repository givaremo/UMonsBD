
/*Load data into Departement*/
call usp_ajoutDepartement("Direction Ressources Humaines");
call usp_ajoutDepartement("Direction Financière");
call usp_ajoutDepartement("Direction Faculté Polytechnique");
call usp_ajoutDepartement("Direction Faculté Sciences de Gestion");
call usp_ajoutDepartement("Direction ICT");
call usp_ajoutDepartement("Direction générale");



/*Load data into Ville*/

call usp_ajoutVille(7332,"Sirault");
call usp_ajoutVille(7332,"Neufmaison");
call usp_ajoutVille(7000,"Mons");
call usp_ajoutVille(6000,"Charleroi");
call usp_ajoutVille(1000,"Bruxelles");
call usp_ajoutVille(5000,"Namur");
call usp_ajoutVille(4000,"Liège");
call usp_ajoutVille(7500,"Tournai");



/*Load data into Adresse*/
call usp_ajoutAdresse("Rue de la bataille","10","",1);
call usp_ajoutAdresse("Rue des Alizés","25","",3);
call usp_ajoutAdresse("Avenue de l'Émeraude","31","B",5);
call usp_ajoutAdresse("Impasse du Zéphyr","20","",4);
call usp_ajoutAdresse("Boulevard des Lumières","1","",6);
call usp_ajoutAdresse("Allée des Charmes","32","",7);
call usp_ajoutAdresse("Place de la Sérénité","14","",8);
call usp_ajoutAdresse("Rue du Cèdre","98","",2);
call usp_ajoutAdresse("Passage des Étoiles","105","A",8);
call usp_ajoutAdresse("Avenue du Papillon","136","",5);
call usp_ajoutAdresse("Chemin du Crépuscule","18","B",3);


/*load data into typeConge*/
call usp_ajoutTypeConge("Congé légal",0);
call usp_ajoutTypeConge("Congé récupération heure supplémentaires",0);
call usp_ajoutTypeConge("Congé force majeure",0);
call usp_ajoutTypeConge("Congé maternité",0);
call usp_ajoutTypeConge("Congé éducation",0);
call usp_ajoutTypeConge("Congé sans solde",1);


/*load data into typeEmploye*/
call usp_ajoutTypeEmploye("Employé RH",0,1,0);
call usp_ajoutTypeEmploye("Manager RH",1,1,0);
call usp_ajoutTypeEmploye("Employé standard",0,0,0);
call usp_ajoutTypeEmploye("Manager standard",1,0,0);
call usp_ajoutTypeEmploye("Directeur Financier",1,0,1);
call usp_ajoutTypeEmploye("Doyen",1,0,0);




/*load some employees*/
call usp_ajoutEmploye("Dupont","Michel","0495/78.32.56","Michel.Dupont@umons.ac.be","toto1234","BE56123456789012","521032-123-56",6,4,6,null) ;
call usp_ajoutEmploye("Dubois","Micheline","0492/23.14.69","Micheline.Dubois@umons.ac.be","toto5678","BE56369874521036","508030-987-23",1,1,2,1) ;
call usp_ajoutEmploye("Lebeau","Marc","0475/69.23.12","Marc.Lebeau@umons.ac.be","abc123","BE23569874526987","653256-236-52",1,6,1,2) ;
call usp_ajoutEmploye("Legrand","Papy","0492/22.01.63","Papy.Legrand@umons.ac.be","azerty99","BE32458963214568","745632-122-33",2,7,5,1) ;

