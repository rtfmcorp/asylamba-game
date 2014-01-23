<?php
// player
define('PAM_BASELVLPLAYER',			1250);
define('PAM_BASELVLCMD',			100);
define('PAM_BASEAP',				10);
define('PAM_COEFFAP',				5);

// impot planete
define('PAM_COEFTAX',				1);

// player statements
define('PAM_ACTIVE', 				1);
define('PAM_INACTIVE',				2);
define('PAM_HOLIDAY',				3);
define('PAM_BANNED',				4);
define('PAM_DELETED',				5);
define('PAM_DEAD',					6);

// player status
define('PAM_STANDARD',				1);
define('PAM_PARLIAMENT',			2);
define('PAM_GOVERNMENT',			3);
define('PAM_CHIEF',					4);

define('PAM_TIME_ALLY_INACTIVE',	24 * 7);
define('PAM_TIME_GLOBAL_INACTIVE',	24 * 15);
define('PAM_TIME_LIMIT_INACTIVE',	24 * 30);