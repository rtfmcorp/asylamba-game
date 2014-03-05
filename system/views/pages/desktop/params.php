<?php
# background paralax
echo '<div id="background-paralax" class="params"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'params/general.php';
	include COMPONENT . 'params/display.php';
echo '</div>';
?>