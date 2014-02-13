<?php
# background paralax
echo '<div id="background-paralax" class="technology"></div>';

# inclusion des elements
include 'technologyElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# player
	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'university') {
		include COMPONENT . 'promethee/university.php';
	} elseif (CTR::$get->get('view') == 'overview') {
		include COMPONENT . 'promethee/overview.php';
	} elseif (CTR::$get->get('view') == 'technos') {
		include COMPONENT . 'promethee/infoTech.php';
	} else {
		CTR::redirect('404');
	}
echo '</div>';
?>