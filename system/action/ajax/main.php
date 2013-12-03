<?php
switch (CTR::$get->get('a')) {
	# ATHENA
	case 'increaseinvestuni':		include AJAX . 'athena/university/increaseInvest.php'; break;
	case 'decreaseinvestuni':		include AJAX . 'athena/university/decreaseInvest.php'; break;

	# HERMES
	case 'readnotif': 				include AJAX . 'hermes/notification/read.php'; break;

	# ARES
	case 'assignship':				include AJAX . 'ares/ship/assign.php'; break;

	# AUTOCOMPLETE
	case 'autocompleteplayer':		include AJAX . 'autocomplete/player.php'; break;

	# XHR RETURN MAPPING
	case 'loadsystem': 				include PAGES . 'ajax/loadSystem.php'; break;
	case 'buildingpanel': 			include PAGES . 'ajax/buildingPanel.php'; break;
	case 'shippanel': 				include PAGES . 'ajax/shipPanel.php'; break;
	case 'technopanel': 			include PAGES . 'ajax/technoPanel.php'; break;
	case 'moremessage': 			include PAGES . 'ajax/message/moreMessage.php'; break;
	case 'morethread': 				include PAGES . 'ajax/message/moreThread.php'; break;

	default:
		CTR::$alert->add('action inconnue ou non-référencée', ALERT_STD_ERROR);
		break;
}
?>