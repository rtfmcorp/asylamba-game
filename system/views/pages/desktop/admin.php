<?php

use Asylamba\Classes\Worker\CTR;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');

# bases loading
if ($session->get('playerInfo')->get('admin') == FALSE) {
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
	if (!$request->query->has('view') OR $request->query->get('view') == 'message') {
		# main message
		include COMPONENT . 'admin/message/newOfficialMessage.php';
		include COMPONENT . 'default.php';
	} elseif ($request->query->get('view') == 'roadmap') {
		# main roadmap
		include COMPONENT . 'admin/roadmap/addEntry.php';
		include COMPONENT . 'default.php';
	} else {
		$this->getContainer()->get('app.response')->redirect('404');
	}
echo '</div>';