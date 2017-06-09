<?php

use Asylamba\Classes\Library\Flashbag;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
# bases loading
//if ($session->get('playerInfo')->get('admin') == FALSE) {
//	$session->addFlashbag('Accès non-autorisé', Flashbag::TYPE_BUG_ERROR);
//	$response->redirect('profil');
//	return;
//}

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