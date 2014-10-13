<?php
switch (CTR::$get->get('a')) {

	# HERMES
	case 'readnotif': 				include AJAX . 'hermes/notification/read.php'; break;
	case 'archivenotif': 			include AJAX . 'hermes/notification/archive.php'; break;
	case 'deletenotif': 			include AJAX . 'hermes/notification/delete.php'; break;

	# ARES
	case 'assignship':				include AJAX . 'ares/ship/assign.php'; break;

	# ATHENA
	case 'increaseinvestuni':		include AJAX . 'zeus/university/increaseInvest.php'; break;
	case 'decreaseinvestuni':		include AJAX . 'zeus/university/decreaseInvest.php'; break;

	# AUTOCOMPLETE
	case 'autocompleteplayer':		include AJAX . 'autocomplete/player.php'; break;
	case 'autocompleteorbitalbase': include AJAX . 'autocomplete/orbitalBase.php'; break;

	# XHR RETURN MAPPING
	case 'loadsystem': 				include PAGES . 'ajax/loadSystem.php'; break;
	case 'buildingpanel': 			include PAGES . 'ajax/buildingPanel.php'; break;
	case 'shippanel': 				include PAGES . 'ajax/shipPanel.php'; break;
	case 'technopanel': 			include PAGES . 'ajax/technoPanel.php'; break;
	case 'moremessage': 			include PAGES . 'ajax/message/moreMessage.php'; break;
	case 'morethread': 				include PAGES . 'ajax/message/moreThread.php'; break;
	case 'morerank': 				include PAGES . 'ajax/morerank.php'; break;

	# XHR RETURN WYSIWYG
	case 'wswpy':					include PAGES . 'ajax/wsw/player.php'; break;

	default:
		CTR::$alert->add('action inconnue ou non-référencée', ALERT_STD_ERROR);
		break;
}
?>