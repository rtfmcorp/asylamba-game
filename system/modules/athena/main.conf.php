<?php
# routes commerciales
define('CRM_CANCELROUTE',			0.8);	# 80% de crédits rendu si on annule la route avant qu'elle soit acceptée
define('CRM_ROUTEBONUSSECTOR', 		1.3);
define('CRM_ROUTEBONUSCOLOR', 		1.7);
define('CRM_COEFEXPERIENCE', 		0.1);

# pourcentage de ressources rendues lors
# de l'annulation d'une construciton de batiment
define('BQM_RESOURCERETURN', 		0.5); 	# 50%

define('SQM_RESOURCERETURN',		0.5);	# 50%

# commercialRoute statement
define('CRM_PROPOSED',				0);
define('CRM_ACTIVE',				1);
define('CRM_STANDBY',				2);

# type of base constants
define('OBM_LEVEL_MIN_TO_CHANGE_TYPE', 	20); # minimal generator level
define('OBM_LEVEL_MIN_FOR_CAPITAL', 	35); # minimal generator level to build a capital base
