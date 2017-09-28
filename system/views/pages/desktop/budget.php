<?php
# background paralax
echo '<div id="background-paralax" class="sponsorship"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';
	include COMPONENT . 'budget/infos.php';
	include COMPONENT . 'budget/donate.php';
	include COMPONENT . 'budget/statistics.php';
echo '</div>';