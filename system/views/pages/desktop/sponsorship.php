<?php

$container = $this->getContainer();
$componentPath = $container->getParameter('component');
# background paralax
echo '<div id="background-paralax" class="sponsorship"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include $componentPath . 'publicity.php';
	include $componentPath . 'sponsorship/infos.php';
	include $componentPath . 'sponsorship/send-mail.php';
	include $componentPath . 'sponsorship/list-godson.php';
echo '</div>';
