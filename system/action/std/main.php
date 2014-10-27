<?php
# démarre la redirection standard vers la page précédente
CTR::redirect();

if (CTR::$get->exist('sftr')) {
	CTR::$data->add('sftr', CTR::$get->get('sftr'));
}

switch (CTR::$get->get('a')) {
	# GENERAL
	case 'switchbase':				include ACTION . 'common/switchBase.php'; break;

	# ATHENA
	case 'updateinvest': 			include ACTION . 'athena/general/updateInvest.php'; break;
	case 'switchdockmode':			include ACTION . 'athena/general/switchDockMode.php'; break;
	case 'createschoolclass':		include ACTION . 'athena/general/createSchoolClass.php'; break;
	case 'giveresource':			include ACTION . 'athena/general/giveResource.php'; break;
	case 'renamebase':				include ACTION . 'athena/general/renameBase.php'; break;
	case 'changebasetype':			include ACTION . 'athena/general/changeBaseType.php'; break;

	case 'buildbuilding':			include ACTION . 'athena/building/build.php'; break;
	case 'dequeuebuilding':			include ACTION . 'athena/building/dequeue.php'; break;

	case 'buildship':				include ACTION . 'athena/ship/build.php'; break;
	case 'dequeueship':				include ACTION . 'athena/ship/dequeue.php'; break;

	case 'proposeroute':			include ACTION . 'athena/route/propose.php'; break;
	case 'acceptroute':				include ACTION . 'athena/route/accept.php'; break;
	case 'refuseroute':				include ACTION . 'athena/route/refuse.php'; break;
	case 'cancelroute':				include ACTION . 'athena/route/cancel.php'; break;
	case 'deleteroute':				include ACTION . 'athena/route/delete.php'; break;

	case 'proposetransaction': 		include ACTION . 'athena/transaction/propose.php'; break;
	case 'accepttransaction':		include ACTION . 'athena/transaction/accept.php'; break;
	case 'canceltransaction':		include ACTION . 'athena/transaction/cancel.php'; break;

	# HERMES
	case 'writemessage': 			include ACTION . 'hermes/message/write.php'; break;
	case 'writeofficial':			include ACTION . 'hermes/message/writeOfficial.php'; break;
	
	case 'readallnotif': 			include ACTION . 'hermes/notification/readAll.php'; break;
	case 'deleteallnotif':			include ACTION . 'hermes/notification/deleteAll.php'; break;
	case 'deletenotif':				include ACTION . 'hermes/notification/delete.php'; break;
	case 'archivenotif': 			include ACTION . 'hermes/notification/archive.php'; break;

	case 'writeradio':				include ACTION . 'hermes/radio/write.php'; break;

	case 'writeroadmap':			include ACTION . 'hermes/roadmap/write.php'; break;

	# PROMETHEE
	case 'buildtechno':				include ACTION . 'promethee/technology/build.php'; break;
	case 'dequeuetechno':			include ACTION . 'promethee/technology/dequeue.php'; break;

	# ZEUS
	case 'searchplayer':			include ACTION . 'zeus/player/searchPlayer.php'; break;
	case 'updateuniinvest':			include ACTION . 'zeus/player/updateUniInvest.php'; break;
	case 'disconnect': 				include ACTION . 'zeus/player/disconnect.php'; break;
	case 'sendcredit': 				include ACTION . 'zeus/player/sendCredit.php'; break;
	case 'sendcredittofaction':		include ACTION . 'zeus/player/sendCreditToFaction.php'; break;

	case 'updatedisplayparams': 	include ACTION . 'zeus/params/display.php'; break;

	case 'validatestep':			include ACTION . 'zeus/tutorial/validateStep.php'; break;

	# ARTEMIS
	case 'spy':						include ACTION . 'artemis/spy.php'; break;
	case 'deletespyreport':			include ACTION . 'artemis/delete.php'; break;
	case 'deleteallspyreport':		include ACTION . 'artemis/deleteAll.php'; break;

	# ARES
	case 'archivereport':			include ACTION . 'ares/report/archive.php'; break;
	case 'deletereport':			include ACTION . 'ares/report/delete.php'; break;
	case 'deleteallreport':			include ACTION . 'ares/report/deleteAll.php'; break;
	
	case 'movefleet':				include ACTION . 'ares/fleet/move.php'; break;
	case 'loot':					include ACTION . 'ares/fleet/loot.php'; break;
	case 'colonize':				include ACTION . 'ares/fleet/colonize.php'; break;
	case 'conquer':					include ACTION . 'ares/fleet/conquer.php'; break;
	case 'cancelmove':				include ACTION . 'ares/fleet/cancel.php'; break;

	case 'affectcommander':			include ACTION . 'ares/commander/affect.php'; break;
	case 'updatenamecommander':		include ACTION . 'ares/commander/updateName.php'; break;
	case 'emptycommander':			include ACTION . 'ares/commander/empty.php'; break;
	case 'firecommander':			include ACTION . 'ares/commander/fire.php'; break;
	case 'changeline':				include ACTION . 'ares/commander/changeLine.php'; break;

	# APOLLON
	case 'writebugreport':			include ACTION . 'apollon/bugReport/write.php'; break;
	case 'archivebugreport':		include ACTION . 'apollon/bugReport/archive.php'; break;
	case 'deletebugreport':			include ACTION . 'apollon/bugReport/delete.php'; break;

	# DEMETER
	case 'writemessageforum':		include ACTION . 'demeter/message/write.php'; break;
	case 'createtopicforum':		include ACTION . 'demeter/topic/createTopic.php'; break;

	case 'postulate':				include ACTION . 'demeter/election/postulate.php'; break;
	case 'makeacoup':				include ACTION . 'demeter/election/makeACoup.php'; break;
	case 'vote':					include ACTION . 'demeter/election/vote.php'; break;
	case 'choosegovernment':		include ACTION . 'demeter/election/chooseGovernment.php'; break;
	case 'fireminister':			include ACTION . 'demeter/election/fire.php'; break;

	case 'votelaw':					include ACTION . 'demeter/law/vote.php'; break;
	case 'createlaw':				include ACTION . 'demeter/law/createLaw.php'; break;


	case 'donate':					include ACTION . 'demeter/donate.php'; break;

	default :
		CTR::$alert->add('action inconnue ou non-référencée', ALERT_STD_ERROR);
		break;
}
?>