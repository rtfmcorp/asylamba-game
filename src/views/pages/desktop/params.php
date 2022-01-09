<?php
$container = $this->getContainer();
$componentPath = $container->getParameter('component');
# background paralax
echo '<div id="background-paralax" class="params"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include $componentPath . 'publicity.php';
	include $componentPath . 'params/general.php';
	include $componentPath . 'params/display.php';
	include $componentPath . 'params/chat.php';
#	include $componentPath . 'params/advertisement.php';
echo '</div>';
