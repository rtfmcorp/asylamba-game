<?php

use App\Classes\Exception\ErrorException;

$container = $this->getContainer();
$ajaxPath = $container->getParameter('ajax');
$pagesPath = $container->getParameter('pages');

switch ($this->getContainer()->get('app.request')->query->get('a')) {
	# COMMON
	case 'switchparams':			include $ajaxPath . 'common/switchParams.php'; break;
	
	# HERMES
	case 'readnotif': 				include $ajaxPath . 'hermes/notification/read.php'; break;
	case 'archivenotif': 			include $ajaxPath . 'hermes/notification/archive.php'; break;
	case 'deletenotif': 			include $ajaxPath . 'hermes/notification/delete.php'; break;

	# ARES
	case 'assignship':				include $ajaxPath . 'ares/ship/assign.php'; break;
	case 'updatesquadron':			include $ajaxPath . 'ares/squadron/update.php'; break;

	# ATHENA
	case 'increaseinvestuni':		include $ajaxPath . 'zeus/university/increaseInvest.php'; break;
	case 'decreaseinvestuni':		include $ajaxPath . 'zeus/university/decreaseInvest.php'; break;

	# AUTOCOMPLETE
	case 'autocompleteplayer':		include $ajaxPath . 'autocomplete/player.php'; break;
	case 'autocompleteorbitalbase': include $ajaxPath . 'autocomplete/orbitalBase.php'; break;

	# XHR RETURN MAPPING
	case 'loadsystem': 				include $pagesPath . 'ajax/loadSystem.php'; break;
	case 'buildingpanel': 			include $pagesPath . 'ajax/buildingPanel.php'; break;
	case 'shippanel': 				include $pagesPath . 'ajax/shipPanel.php'; break;
	case 'technopanel': 			include $pagesPath . 'ajax/technoPanel.php'; break;
	case 'moremessage': 			include $pagesPath . 'ajax/conversation/message.php'; break;
	case 'moreconversation': 		include $pagesPath . 'ajax/conversation/conversation.php'; break;
	case 'morerank': 				include $pagesPath . 'ajax/morerank.php'; break;

	# XHR RETURN WYSIWYG
	case 'wswpy':					include $pagesPath . 'ajax/wsw/player.php'; break;
	case 'wswpl':					include $pagesPath . 'ajax/wsw/place.php'; break;

	default:
		throw new ErrorException('action inconnue ou non-référencée');
}
