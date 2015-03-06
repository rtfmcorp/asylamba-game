<?php
# bases loading
if (CTR::$data->get('playerInfo')->get('admin') == FALSE) {
	header('Location: ' . APP_ROOT . 'profil');
	exit();
}

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'adminElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# admin component
	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'message') {
		# main message
		include COMPONENT . 'admin/message/newOfficialMessage.php';
	} elseif (CTR::$get->get('view') == 'roadmap') {
		# main roadmap
		include COMPONENT . 'admin/roadmap/addEntry.php';
	} else {
		CTR::redirect('404');
	}
echo '</div>';
?>