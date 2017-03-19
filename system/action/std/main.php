<?php

use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');

# démarre la redirection standard vers la page précédente
$response->redirect($session->getLastHistory());

if ($request->query->has('sftr')) {
	$session->add('sftr', $request->query->get('sftr'));
}

if ($request->query->has('token') && $session->get('token') === $request->query->get('token')) {
	switch ($request->query->get('a')) {
		# GENERAL
		case 'switchbase': 				include ACTION . 'common/switchBase.php'; break;
		case 'switchparams':			include ACTION . 'common/switchParams.php'; break;
		case 'sendsponsorshipemail': 	include ACTION . 'common/sendSponsorshipEmail.php'; break;
		case 'discordrequest': 			include ACTION . 'common/discordRequest.php'; break;

		# ATHENA
		case 'updateinvest': 			include ACTION . 'athena/general/updateInvest.php'; break;
		case 'switchdockmode':			include ACTION . 'athena/general/switchDockMode.php'; break;
		case 'createschoolclass':		include ACTION . 'athena/general/createSchoolClass.php'; break;
		case 'giveresource':			include ACTION . 'athena/general/giveResource.php'; break;
		case 'giveships':				include ACTION . 'athena/general/giveShips.php'; break;
		case 'renamebase':				include ACTION . 'athena/general/renameBase.php'; break;
		case 'changebasetype':			include ACTION . 'athena/general/changeBaseType.php'; break;
		case 'leavebase':				include ACTION . 'athena/general/leaveBase.php'; break;

		case 'buildbuilding':			include ACTION . 'athena/building/build.php'; break;
		case 'dequeuebuilding':			include ACTION . 'athena/building/dequeue.php'; break;

		case 'buildship':				include ACTION . 'athena/ship/build.php'; break;
		case 'dequeueship':				include ACTION . 'athena/ship/dequeue.php'; break;
		case 'recycleship':				include ACTION . 'athena/ship/recycle.php'; break;

		case 'proposeroute':			include ACTION . 'athena/route/propose.php'; break;
		case 'acceptroute':				include ACTION . 'athena/route/accept.php'; break;
		case 'refuseroute':				include ACTION . 'athena/route/refuse.php'; break;
		case 'cancelroute':				include ACTION . 'athena/route/cancel.php'; break;
		case 'deleteroute':				include ACTION . 'athena/route/delete.php'; break;

		case 'proposetransaction': 		include ACTION . 'athena/transaction/propose.php'; break;
		case 'accepttransaction':		include ACTION . 'athena/transaction/accept.php'; break;
		case 'canceltransaction':		include ACTION . 'athena/transaction/cancel.php'; break;

		case 'createmission':			include ACTION . 'athena/recycling/createMission.php'; break;
		case 'cancelmission':			include ACTION . 'athena/recycling/cancelMission.php'; break;
		case 'addtomission': 			include ACTION . 'athena/recycling/addToMission.php'; break;

		# HERMES
		case 'startconversation': 		include ACTION . 'hermes/conversation/start.php'; break;
		case 'writeconversation': 		include ACTION . 'hermes/conversation/write.php'; break;
		case 'leaveconversation':		include ACTION . 'hermes/conversation/leave.php'; break;
		case 'adduserconversation':		include ACTION . 'hermes/conversation/addUser.php'; break;
		case 'updatedisplayconversation':include ACTION .'hermes/conversation/updateDisplay.php'; break;
		case 'updatetitleconversation':	include ACTION . 'hermes/conversation/updateTitle.php'; break;
		case 'writeofficialconversation':include ACTION .'hermes/conversation/writeOfficial.php'; break;
		case 'writefactionconversation':include ACTION . 'hermes/conversation/writeFaction.php'; break;
		
		case 'readallnotif': 			include ACTION . 'hermes/notification/readAll.php'; break;
		case 'deleteallnotif':			include ACTION . 'hermes/notification/deleteAll.php'; break;
		case 'deletenotif':				include ACTION . 'hermes/notification/delete.php'; break;
		case 'archivenotif': 			include ACTION . 'hermes/notification/archive.php'; break;

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
		case 'sendcreditfromfaction':	include ACTION . 'zeus/player/sendCreditFromFaction.php'; break;
		case 'abandonserver': 			include ACTION . 'zeus/player/abandonServer.php'; break;
		case 'switchadvertisement':		include ACTION . 'zeus/player/switchAdvertisement.php'; break;

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
		case 'putcommanderinschool':	include ACTION . 'ares/commander/putInSchool.php'; break;
		case 'updatenamecommander':		include ACTION . 'ares/commander/updateName.php'; break;
		case 'emptycommander':			include ACTION . 'ares/commander/empty.php'; break;
		case 'firecommander':			include ACTION . 'ares/commander/fire.php'; break;
		case 'changeline':				include ACTION . 'ares/commander/changeLine.php'; break;

		# DEMETER
		case 'writemessageforum':		include ACTION . 'demeter/message/write.php'; break;
		case 'movetopicforum':			include ACTION . 'demeter/topic/move.php'; break;
		case 'closetopicforum':			include ACTION . 'demeter/topic/close.php'; break;
		case 'uptopicforum':			include ACTION . 'demeter/topic/up.php'; break;
		case 'archivetopicforum':		include ACTION . 'demeter/topic/archive.php'; break;
		case 'editmessageforum':		include ACTION . 'demeter/message/edit.php'; break;
		case 'createtopicforum':		include ACTION . 'demeter/topic/createTopic.php'; break;

		case 'writenews':				include ACTION . 'demeter/news/write.php'; break;
		case 'editnews':				include ACTION . 'demeter/news/edit.php'; break;
		case 'pinnews':					include ACTION . 'demeter/news/pin.php'; break;
		case 'deletenews':				include ACTION . 'demeter/news/delete.php'; break;

		case 'postulate':				include ACTION . 'demeter/election/postulate.php'; break;
		case 'makeacoup':				include ACTION . 'demeter/election/makeACoup.php'; break;
		case 'vote':					include ACTION . 'demeter/election/vote.php'; break;
		case 'choosegovernment':		include ACTION . 'demeter/election/chooseGovernment.php'; break;
		case 'fireminister':			include ACTION . 'demeter/election/fire.php'; break;
		case 'resign':					include ACTION . 'demeter/election/resign.php'; break;
		case 'abdicate':				include ACTION . 'demeter/election/abdicate.php'; break;

		case 'votelaw':					include ACTION . 'demeter/law/vote.php'; break;
		case 'createlaw':				include ACTION . 'demeter/law/createLaw.php'; break;

		case 'updatefactiondesc':		include ACTION . 'demeter/updateFactionDesc.php'; break;

		case 'donate':					include ACTION . 'demeter/donate.php'; break;

		default :
			throw new ErrorException('action inconnue ou non-référencée');
	}
} elseif ($request->query->get('a') == 'switchbase') {
	# action sans token
	include ACTION . 'common/switchBase.php';
} else {
	throw new ErrorException('votre token CSRF a expiré');
}