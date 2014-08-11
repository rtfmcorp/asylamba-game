<?php
# set player description action

CTR::$cookie->add('movers', 	(bool)Utils::getHTTPData('movers'));
CTR::$cookie->add('anims', 		(bool)Utils::getHTTPData('anims'));
CTR::$cookie->add('panel', 		(bool)Utils::getHTTPData('panel'));
CTR::$cookie->add('minimap', 	(bool)Utils::getHTTPData('minimap'));
CTR::$cookie->add('rc', 		(bool)Utils::getHTTPData('rc'));
CTR::$cookie->add('spying', 	(bool)Utils::getHTTPData('spying'));
CTR::$cookie->add('movements', 	(bool)Utils::getHTTPData('movements'));
CTR::$cookie->add('attacks', 	(bool)Utils::getHTTPData('attacks'));
?>