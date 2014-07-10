<?php
/* DUPLIQUER SUR COMMANDER.CLASS */
define('COM_COEFFEARNEDEXP', 	50);
define('COEFFEXPPLAYER',		100);
define('COM_CMDBASELVL', 		100);

define('COEFFMOVEINSYSTEM', 	584);
define('COEFFMOVEOUTOFSYSTEM', 	600);
define('COEFFMOVEINTERSYSTEM', 	1000);

define('COM_LVLINCOMECOMMANDER', 100);

define('CREDITCOEFFTOCOLONIZE',	100000);
define('CREDITCOEFFTOCONQUER',	250000);

// dans TaskFleetController
define('LIMITTOLOOT', 			5000);
define('COEFFLOOT', 			250);

// Commander statement
define('COM_INSCHOOL', 			0);
define('COM_AFFECTED', 			1);
define('COM_MOVING', 			2);
define('COM_DEAD', 				3);
define('COM_DESERT', 			4); // déserté
define('COM_RETIRED', 			5); // à la retraite
define('COM_ONSALE', 			6);

// Commander type of move
define('COM_MOVE',				0);
define('COM_LOOT',				1);
define('COM_COLO',				2);	// colo ou conquete
define('COM_BACK',				3);	// retour