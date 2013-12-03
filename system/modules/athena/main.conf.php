<?php

// routes commerciales
define('CRM_COEFROUTEPRICE',		28);
define('CRM_COEFROUTEINCOME',		0.2);
define('CRM_CANCELROUTE',			0.8);	// 80% de crédits rendu si on annule la route avant qu'elle soit acceptée
define('CRM_ROUTEBONUSSECTOR', 		1.1);
define('CRM_ROUTEBONUSCOLOR', 		1.4);
define('CRM_COEFEXPERIENCE', 		0.1);

// queue de construction de bâtiments
define('BQM_MAXQUEUE', 				4);

// pourcentage de ressources rendues lors
// de l'annulation d'une construciton de batiment
define('BQM_RESOURCERETURN', 		0.5); 	// 50%

// queue de construction de vaisseaux
define('SQM_SHIPMAXQUEUE', 			5);
define('SQM_RESOURCERETURN',		0.5);	// 50%

// commandant
define('MAXCOMMANDERINSCHOOL',		10); // --> à vérifier si on utilise toujours

// commercialRoute statement
define('CRM_PROPOSED',				0);
define('CRM_ACTIVE',				1);
define('CRM_STANDBY',				2);

// production/storage mode de la raffinerie
define('OBM_COEFPRODUCTION', 		0.1);

// limite du stock de ressources pour pouvoir en envoyer
define('OBM_STOCKLIMIT', 			0);	// 40%